<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    // Listar notas
    public function index(Request $request)
    {
        $query = Note::query();

        // Ordenar por fecha de creación o vencimiento
        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $query->orderBy($sortBy);
        }

        return response()->json($query->get());
    }

    // Crear nota
    public function store(Request $request)
    {
        // Verifica si el usuario está autenticado
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validar los datos de entrada
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date_format:Y-m-d',
            'image' => 'nullable|string',
        ]);

        try {
            // Crear la nota
            $note = new Note();
            $note->title = $request->input('title');
            $note->description = $request->input('description');
            $note->user_id = Auth::id();
            $note->due_date = $request->input('due_date');
            $note->image = $request->input('image');
            $note->save();

            // Asignar etiquetas (opcional)
            if ($request->has('tags')) {
                // Llama al método en TagController
                $tagController = new TagController();
                $tagController->assignTagsToNote($note, $request->input('tags'));
            }

            return response()->json($note, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }




    // Obtener nota específica
    public function show(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($note);
    }

    // Editar nota
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50', // Cada etiqueta debe ser una cadena con un máximo de 50 caracteres
            'due_date' => 'nullable|date',
            'image' => 'nullable|string',
        ]);

        try {
            // Actualizar la nota con los datos de la solicitud
            $note->update($request->all());

            // Actualizar etiquetas (si están presentes)
            if ($request->has('tags')) {
                // Obtener los IDs de las etiquetas
                $tagIds = [];
                foreach ($request->input('tags') as $tagName) {
                    // Obtener o crear la etiqueta
                    $tag = Tag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id; // Agregar el ID de la etiqueta al arreglo
                }

                // Sincronizar las etiquetas usando los IDs
                $note->tags()->sync($tagIds);
            }

            // Retornar la nota actualizada
            return response()->json($note, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar la nota: ' . $e->getMessage()], 500);
        }
    }



    // Eliminar nota
    public function destroy(Note $note)
    {
        $note->delete();

        return response()->json(null, 204);
    }
}
