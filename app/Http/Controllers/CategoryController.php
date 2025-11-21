<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Mockery\Exception;

class CategoryController extends Controller
{
    //

    public function index()
    {

         $categories =  DB::table('servicecategories')->paginate(50);
         //print_r($categories);
        return view('categorylist', ['categories' => $categories]);
        //return View('addcategory');
    }


    public function create()
    {
        return View('addcategory');
    }


    public function store(Request $request)
    {
       $validator =  Validator::make( $request->all(), ['name' => 'required|max:255'] );

        if ($validator->fails()) {
            return redirect( route('category.create') )
                ->withInput()
                ->withErrors($validator);
        }
        else
        {
            $category = new Category;
            $category->name = $request->name;
            if( !isset($request->description ) )
            {
                $request->description = '';
            }
            $category->description = $request->description;
            $category->status = '1';
            $category->save();
            $request->session()->flash("successmsg", "Successfully Added.");
            return redirect()->back();
        }
        return View('addcategory');
    }




    public function edit($id)
    {


            $category = Category::find($id);


          if(!$category) {
         // echo "pppp";
         abort(404,"Not Found");
           }

            //return redirect(url('category'))
               // ->withErrors(['error1' => 'Not found']);

         return view('categoryedit', ['category' => $category]);
        //print_r($category);
    }



    public function update(Request $request, $id)
    {
        $validator =  Validator::make( $request->all(), ['name' => 'required|max:255'] );
        if ($validator->fails()) {
            return redirect(route('category.edit'))
                ->withInput()
                ->withErrors($validator);
        }
        else {


            $category = Category::findOrFail($id);
             if(  $request->description != '' )
             {
                 $category->description = $request->description;
             }
             else
             {
                 $category->description = '';
             }
            $input = $request->all();
            $category->name = $request->name;
            $category->save();
            // $category->name = $request->name;
            //$category->description = $request->description;
            $request->session()->flash("successmsg", "Successfully Updated.");
        }
        return redirect()->back();
    }

    public function destroy($id)
    {
        //Category::destroy($id);
        //Session::flash('successmsg', 'Successfully deleted!');
        return redirect()->route('category.index');
    }
}
