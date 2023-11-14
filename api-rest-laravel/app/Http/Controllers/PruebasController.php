<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;

class PruebasController extends Controller
{
    //
    public function testOrm(){
        /*$posts=Post::all();
        foreach($posts as $post){
            echo "<h1>".$post->title."</h1>";
            echo "<span>".$post->user->name."</span>";
        }*/
         $categories=Category::all();

         foreach ($categories as $category) {
            # code...
            //echo "<h1>{$category->name}</h1>";
            
            foreach ($category->posts as $post) {
                # code...
                echo "<h2>{$post->title}</h2>";
                echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";
                //echo "<h3>{$post->user->name}</h3>";
                echo "<p>{$post->content}</p>";
                echo "<hr>";
            }
         }



        die();
    }
}
