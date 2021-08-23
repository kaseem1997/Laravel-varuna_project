<?php



namespace App\Http\Controllers\Admin;



use App\BlogCategory;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Validator;

use Storage;

use App\Helpers\CustomHelper;

use DB;

use Image;



class BlogCategoryController extends Controller{



    private $limit;

    private $ADMIN_ROUTE_NAME;



    public function __construct(){

        $this->limit = 20;

        $this->ADMIN_ROUTE_NAME = CustomHelper::getAdminRouteName();

    }



    public function index(Request $request){



        $data = [];



        $limit = $this->limit;

        $type = (isset($request->type))?$request->type:'';

        $parent_id = (isset($request->parent_id))?$request->parent_id:'';

        $name = (isset($request->name))?$request->name:'';
        $status = (isset($request->status))?$request->status:'';
        $from = (isset($request->from))?$request->from:'';
        $to = (isset($request->to))?$request->to:'';



        if($type == 'blogs' || $type == 'news'){

            $category_query = BlogCategory::where(['parent_id'=>$parent_id])->orderBy('id', 'desc');

            if($type !=''){
                $category_query->where('type', $type);
            }

            if(!empty($name)){
                $category_query->where('name', 'like', $name.'%');
            }

            if($status !=''){
                $category_query->where('status',$status);
            }

            if(!empty($from)){
                $from_date = CustomHelper::DateFormat($from, 'Y-m-d', 'd/m/Y');
                if(!empty($from_date)){
                    $category_query->whereRaw('DATE(action_date) >= "'.$from_date.'"');
                }
            }
            if(!empty($to)){
                $to_date = CustomHelper::DateFormat($to, 'Y-m-d', 'd/m/Y');
                if(!empty($to_date)){
                   $category_query->whereRaw('DATE(action_date) <= "'.$to_date.'"');
               }
           }



            $categories = $category_query->paginate($limit);

            $parentCategory = '';
            if(is_numeric($parent_id) && $parent_id > 0){
                $parentCategory = BlogCategory::where('id', $parent_id)->first();
            }


            $countParents = 0;
            if(!empty($parentCategory) && count($parentCategory) > 0){

                $countParents++;

                if($parentCategory->parent && count($parentCategory->parent) > 0){

                    $countParents++;

                    $pParentCategory = $parentCategory->parent;

                    if($pParentCategory->parent && count($pParentCategory->parent) > 0){

                        $countParents++;

                        $ppParentCategory = $pParentCategory->parent;
                    }
                }
            }

            $data['parent_id'] = $parent_id;

            $data['parentCategory'] = $parentCategory;

            $data['countParents'] = $countParents;

            $data['categories'] = $categories;

            $data['limit'] = $limit;

            $data['type'] = $type;

        }

        else{

            return redirect(url($this->ADMIN_ROUTE_NAME));

        }

        return view('admin.blogs_categories.index', $data);



    }





    public function add(Request $request){

        //prd($request->toArray());

        $data = [];

        $id = (isset($request->id))?$request->id:0;

        $type = (isset($request->type))?$request->type:'';

        $parent_id = (isset($request->parent_id))?$request->parent_id:0;

        //prd($type);

        if($type == 'blogs' || $type == 'news'){



        $category = '';



        if(is_numeric($id) && $id > 0){

            $category = BlogCategory::find($id);

        }



        if($request->method() == 'POST' || $request->method() == 'post'){

            //prd($request->toArray());



            $back_url = (isset($request->back_url))?$request->back_url:'';



            if(empty($back_url)){

                $back_url = $this->ADMIN_ROUTE_NAME.'/blogs_categories';

            }



            $req_id = (isset($request->id))?$request->id:0;



            $rules = [];

            $rules['name'] = 'required';

            $rules['status'] = 'required';



            $this->validate($request, $rules);



            $req_data = [];



            $req_data = $request->except(['_token', 'id','back_url']);

            

            $slug = CustomHelper::GetSlug('blog_categories', 'id', $id, $request->name);

            $req_data['slug'] = $slug;

            //prd($req_data);

            if(!empty($category) && count($category) > 0 && $req_id == $id){

                $isSaved = BlogCategory::where(['id'=>$category->id, 'type'=>$type])->update($req_data);

                //prd($isSaved);

            }

            else{

                $isSaved = BlogCategory::create($req_data);

                $id = $isSaved->id;

            }

            

            if ($isSaved) {



                return redirect(url($back_url))->with('alert-success', 'Category has been saved successfully.');

            } else {

                return back()->with('alert-danger', 'Category cannot be added, please try again or contact the administrator.');

            }

        }

        

        if($type == 'blogs'){

            $page_heading = 'Add Insight Category';

            if(isset($category->name)){

                $page_heading = 'Edit Insight Category - '.$category->name;

            }

        }

        elseif($type == 'news'){

            $page_heading = 'Add Case Study Category';

            if(isset($category->name)){

                $page_heading = 'Edit Case Study Category - '.$category->name;

            }



        }

        $parent_category = '';
        if(is_numeric($parent_id) && $parent_id > 0){
            $parent_category = BlogCategory::find($parent_id);
        }



        $data['parent_category'] = $parent_category;
        $data['page_heading'] = $page_heading;

        $data['parent_id'] = $parent_id;

        $data['id'] = $id;

        $data['category'] = $category;

        $data['type'] = $type;

        }



        else{

            return redirect(url($this->ADMIN_ROUTE_NAME));

        }





        return view('admin.blogs_categories.form', $data);



    }



    public function delete($id){

        $category = BlogCategory::find($id);

        if ($category->blogs() && $category->blogs()->count() > 0) {

            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->blogs()->count() . ' blogs associated with it. Please remove the blogs first.');

        }

        // The Category must not have any associated Sub-categories to be deleted

        if ($category->children() && $category->children()->count() > 0) {

            return back()->with('alert-danger', 'This category cannot be removed because there are currently ' . $category->children()->count() . ' blog sub-categories associated with it. Please remove the blog sub-categories first.');

        }

        else {      

            $category->delete();



            return back()->with('alert-success', 'The category has been removed successfully.');

        }

    }



    /* end of controller */

}