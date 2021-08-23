@component('admin.layouts.main')

    @slot('title')
        Admin - {{ $page_heading }} - {{ config('app.name') }}
    @endslot

    @slot('headerBlock')

    <link rel="stylesheet" type="text/css" href="{{ url('css/jquery-ui.css') }}"/ >

    <link rel="stylesheet" type="text/css" href="{{ url('datetimepicker/jquery-ui-timepicker-addon.css') }}"/ >

    @endslot


    <?php

    //pr($blog->toArray());
    $routeName = CustomHelper::getAdminRouteName();

    $id = (isset($media->id))?$media->id:'';
    $category_id = (isset($media->category_id))?$media->category_id:'';
    $title = (isset($media->title))?$media->title:'';
    $slug = (isset($media->slug))?$media->slug:'';
    $brief = (isset($media->brief))?$media->brief:'';
    $description = (isset($media->description))?$media->description:'';
    $sub_title = (isset($media->sub_title))?$media->sub_title:'';
    $sort_order = (isset($media->sort_order))?$media->sort_order:0;
    $author_name = (isset($media->author_name))?$media->author_name:'';
    $meta_title = (isset($media->meta_title))?$media->meta_title:'';
    $meta_keyword = (isset($media->meta_keyword))?$media->meta_keyword:'';
    $meta_description = (isset($media->meta_description))?$media->meta_description:'';
    $featured = (isset($media->featured))?$media->featured:0;
    $status = (isset($media->status))?$media->status:1;
    $image = (isset($media->image))?$media->image:'';

    $pages_id = (isset($media->pages_id))?$media->pages_id:'';

    if(!empty($pages_id) && count((array)$pages_id) > 0){
        $pages_id = explode(',', $pages_id);
    }

    $storage = Storage::disk('public');

    //pr($storage);

    $path = 'media/';

    $old_image = 0;

    $image_req = 'required';
    $link_req = '';
    ?>

    <div class="row">

        <div class="col-md-12 bgcolor">

            <h2>{{ $page_heading }} <?php if(request()->has('back_url')){ $back_url= request('back_url');  ?>
            
        <a href="{{ url($back_url)}}" class="btn btn-success btn-sm" style='float: right;'>Back</a><?php } ?>
    </h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="ajax_msg"></div>

            <form method="POST" action="" accept-charset="UTF-8" role="form" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label required">Title:</label>

                            <input type="text" id="title" class="form-control" name="title" value="{{ old('title', $title) }}" />

                            @include('snippets.errors_first', ['param' => 'title'])
                        </div>
                    </div>

                    <?php
                    if(!empty($slug)){
                    ?>
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('slug') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Slug:</label>

                            <input type="text" id="slug" class="form-control" name="slug" value="{{ old('slug', $slug) }}" required  />

                            @include('snippets.errors_first', ['param' => 'slug'])
                        </div>
                    </div>
                    <?php } ?>

                    <?php /*
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('category_id') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Category:</label>

                            <select name="category_id" class="form-control">
                                <option value="">Select</option>
                                <?php
                                if(!empty($categories) && count($categories) > 0){

                                    foreach($categories as $cat){
                                        
                                        $selected = '';

                                        if( $cat->id == $category_id ){
                                            $selected = 'selected';
                                        }

                                        //pr($category_hierarchy);
                                        ?>
                                        <option value="{{$cat->id}}" {{$selected}} >{{$cat->name}}</option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>


                            @include('snippets.errors_first', ['param' => 'category_id'])
                        </div>
                    </div> */?>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('pages_id') ? ' has-error' : '' }}">
                        <label for="pages_id" class="control-label">Select Services:</label>
                            <select name="pages_id[]" class="form-control page_id" multiple="multiple">
                            <?php
                            if(!empty($cms_pages) && count((array)$cms_pages) > 0){
                                foreach ($cms_pages as $cms){
                                    $selected = '';
                                    
                                    if(!empty($pages_id) && count((array)$pages_id) > 0){
                                        if(in_array($cms->id, $pages_id)){
                                            $selected = 'selected';
                                        }
                                    }
                                    
                            ?>
                              <option value="{{$cms->id}}" {{$selected}}>{{$cms->title}}</option>
                            <?php } } ?>
                          </select>

                            @include('snippets.errors_first', ['param' => 'pages_id'])
                        </div>
                    </div>

                    

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('sub_title') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Sub Title:</label>

                            <textarea id="sub_title" class="form-control " name="sub_title">{{ old('sub_title', $sub_title) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'sub_title'])
                        </div>
                    </div> 
                
                
                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('brief') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Brief:</label>

                            <textarea id="brief" class="form-control " name="brief">{{ old('brief', $brief) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'brief'])
                        </div>
                    </div> 

                    <div class="col-md-12">
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Description:</label>

                            <textarea id="description" class="form-control " name="description">{{ old('description', $description) }}</textarea>

                            @include('snippets.errors_first', ['param' => 'description'])
                        </div>
                    </div>

                    <?php /*
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('author_name') ? ' has-error' : '' }}">
                            <label for="author_name" class="control-label">Author Name:</label>

                            <input type="text" id="author_name" class="form-control" name="author_name" value="{{ old('author_name', $author_name) }}" />

                            @include('snippets.errors_first', ['param' => 'author_name'])
                        </div>
                    </div> */ ?>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
                            <label for="sort_order" class="control-label">Sort Order:</label>

                            <input type="text" id="sort_order" class="form-control" name="sort_order" value="{{ old('sort_order', $sort_order) }}" />

                            @include('snippets.errors_first', ['param' => 'sort_order'])
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_title') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Mata Title:</label>

                            <input type="text" id="meta_title" class="form-control" name="meta_title" value="{{ old('meta_title', $meta_title) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_title'])
                        </div>
                    </div>

                    <?php /*
                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_keyword') ? ' has-error' : '' }}">
                            <label for="link_name" class="control-label">Mata Keyword:</label>

                            <input type="text" id="meta_keyword" class="form-control" name="meta_keyword" value="{{ old('meta_keyword', $meta_keyword) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_keyword'])
                        </div>
                    </div> */?>

                    <div class="col-md-6">
                        <div class="form-group{{ $errors->has('meta_description') ? ' has-error' : '' }}">
                            <label for="meta_description" class="control-label">Mata Description:</label>

                            <input type="text" id="meta_description" class="form-control" name="meta_description" value="{{ old('meta_description', $meta_description) }}"  />

                            @include('snippets.errors_first', ['param' => 'meta_description'])
                        </div>
                    </div>
                


                <?php
                $image_required = $image_req;
                if($id > 0){
                    $image_required = '';
                }
                ?>
                    <div class="col-md-6">

                                <div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
                                    <label for="sort_order" class="control-label {{ $image_required }}">Image:</label>

                                    <input type="file" id="image" name="image"/>

                                    @include('snippets.errors_first', ['param' => 'image'])
                                </div>
                                <?php
                                if(!empty($image)){
                                    if($storage->exists($path.$image))
                                    {
                                        ?>
                                        <div class="col-md-2 image_box">
                                           <img src="{{ url('storage/'.$path.'thumb/'.$image) }}" style="width: 100px;"><br>
                                           <a href="javascript:void(0)" data-id="{{ $id }}" class="del_image">Delete</a>
                                       </div>
                                       <?php        
                                   }

                                   ?>
                                   <?php
                               }
                               ?>

                           <input type="hidden" name="old_image" value="{{ $old_image }}">
                        
                 </div>

                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }} col-md-12">
                            <label class="control-label">Status:</label>
                            &nbsp;&nbsp;
                            Active: <input type="radio" name="status" value="1" <?php echo ($status == '1')?'checked':''; ?> >
                            &nbsp;
                            Inactive: <input type="radio" name="status" value="0" <?php echo ( strlen($status) > 0 && $status == '0')?'checked':''; ?> >

                            @include('snippets.errors_first', ['param' => 'status'])
                        </div>

                        <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }} col-md-12">
                            <div class="form-group{{ $errors->has('featured') ? ' has-error' : '' }}">
                                <label class="control-label ">Featured:</label>

                                <input type="checkbox" name="featured" value="1" <?php echo ($featured == '1')?'checked':''; ?> />

                                @include('snippets.errors_first', ['param' => 'featured'])
                            </div>
                        </div>
                
                
                            <div class="col-md-12">
                                <div class="form-group">
                                    <p></p>
                                    <input type="hidden" id="id" class="form-control" name="id" value="{{ old('id', $id) }}"  />
                                    <button type="submit" class="btn btn-success" title="Create this new category"><i class="fa fa-save"></i> Submit</button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </form>
            <div class="clearfix"></div>
        </div>

    </div>




@slot('bottomBlock')

<script type="text/javascript" src="{{ url('js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ url('datetimepicker/jquery-ui-timepicker-addon.js') }}"></script>

<script type="text/javascript" src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


<script type="text/javascript">

$(document).ready(function(){

    $('.page_id').select2({
    });

    $(".del_image").click(function(){

        var current_sel = $(this);

        var image_id = $(this).attr('data-id');

        conf = confirm("Are you sure to Delete this Image?");

        if(conf){

            var _token = '{{ csrf_token() }}';

            $.ajax({
                url: "{{ route($routeName.'.media.ajax_delete_image') }}",
                type: "POST",
                data: {image_id},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                cache: false,
                beforeSend:function(){
                   $(".ajax_msg").html("");
                },
                success: function(resp){
                    if(resp.success){
                        $(".ajax_msg").html(resp.msg);
                        current_sel.parent('.image_box').remove();
                    }
                    else{
                        $(".ajax_msg").html(resp.msg);
                    }
                    
                }
            });
        }

    });


});
    
$(document).ready(function(){

//var editor = CKEDITOR.replace('brief');
//var editor = CKEDITOR.replace('description');



var editor = CKEDITOR.replace('description', {
    filebrowserImageUploadUrl: "<?php echo url($routeName.'/ck_upload?type=cms_pages&csrf_token='.csrf_token());?>"
});

$('.date').datepicker({
    dateFormat: "dd/mm/yy",
    changeMonth: true,
    changeYear: true,
    yearRange:"1950:+0"
});

});
 </script>


@endslot

@endcomponent