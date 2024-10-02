<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Note;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Método para asignar etiquetas a una nota
    public function assignTagsToNote(Note $note, array $tags)
    {
        $tagIds = [];

        foreach ($tags as $tagName) {
            // Buscar la etiqueta por nombre
            $tag = Tag::where('name', $tagName)->first();

            if ($tag) {
                // Si la etiqueta existe, agregamos su ID
                $tagIds[] = $tag->id;
            } else {
                // Si la etiqueta no existe, la creamos
                $newTag = Tag::create(['name' => $tagName]);
                $tagIds[] = $newTag->id;
            }
        }

        // Asociar las etiquetas a la nota
        $note->tags()->sync($tagIds);
    }
}
