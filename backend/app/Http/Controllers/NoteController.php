<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    // Listar notas
    public function index(Request $request, $userId)
    {
        $query = Note::query();


        $query->where('user_id', $userId);


        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $query->orderBy($sortBy);
        }

        // Obtener notas con solo los nombres de las etiquetas
        $notes = $query->with('tags:id,name')->get();


        $notes = $notes->map(function ($note) {
            return [
                'id' => $note->id,
                'title' => $note->title,
                'description' => $note->description,
                'user_id' => $note->user_id,
                'due_date' => $note->due_date,
                'image' => $note->image ? 'data:image/png;base64,' . base64_encode($note->image) : null,
                'tags' => $note->tags->pluck('name')
            ];
        });

        return response()->json($notes);
    }



    // Crear nota
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date_format:Y-m-d',
            'tags' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Crear la nota
            $note = new Note();
            $note->title = $request->input('title');
            $note->description = $request->input('description');
            $note->user_id = $request->input('user_id');
            $note->due_date = $request->input('due_date');

            // Manejo de la imagen
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('images', 'public');
                $note->image = $imagePath;
            }

            $note->save();

            // Asignar etiquetas (opcional)
            if ($request->has('tags')) {

                $tagController = new TagController();
                $tagController->assignTagsToNote($note, $request->input('tags'));
            }

            return response()->json($note, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
