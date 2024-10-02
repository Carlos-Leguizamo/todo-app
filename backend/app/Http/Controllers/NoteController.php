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


    public function update(Request $request, Note $note)
{
    // Validar los datos de entrada
    $request->validate([
        'title' => 'string|max:255',
        'description' => 'nullable|string',
        'tags' => 'nullable|array',
        'tags.*' => 'string|max:50',
        'due_date' => 'nullable|date',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        // Capturamos solo los campos que se pueden actualizar
        $dataToUpdate = $request->only(['title', 'description', 'due_date']);

        // Actualizar la nota con los datos válidos
        $note->update($dataToUpdate);

        // Manejo de la imagen
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('images', 'public'); // Guarda la nueva imagen
            $note->image = $imagePath; // Actualiza la ruta de la imagen
        }

        // Actualizar etiquetas
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($request->input('tags') as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $note->tags()->sync($tagIds); // Sincroniza las etiquetas
        }

        // Guardar los cambios en la nota
        $note->save(); // Guarda los cambios en la nota

        // Retornar la nota actualizada
        return response()->json($note->fresh(), 200); // Utiliza fresh() para obtener los datos más recientes
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
