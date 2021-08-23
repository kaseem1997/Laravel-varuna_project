@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Blogs - {{ config('app.name') }}
    @endslot

    <?php 
    $back_url = CustomHelper::BackUrl();
    $routeName = CustomHelper::getAdminRouteName();

    $addBtn = '';
    $title = '';
    
    if($type == 'blogs'){
        $addBtn = 'Add Insights';
        $title = 'Manage Insights';
    }
    elseif($type == 'news'){
        $addBtn = 'Add Case Study';
        $title = 'Manage Case Study';
    }

    $old_title = app('request')->input('title');
    $old_vehicle_no = app('request')->input('vehicle_no');
    $old_email = app('request')->input('email');
    $old_phone = app('request')->input('phone');
    $old_status = app('request')->input('status');
    $old_featured = app('request')->input('featured');
    $old_from = app('request')->input('from');
    $old_to = app('request')->input('to');
    $old_category_id = app('request')->input('category_id');

    ?>
    <div class="row">

        <div class="col-md-12">

            <h2>{{$title}}
                <a href="{{route($routeName.'.blogs.add',['type'=>$type,'back_url'=>$back_url]) }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> {{$addBtn}}</a>
            
            </h2>

            <div class="row">

            <div class="col-md-12">
                <div class="bgcolor">

                    <div class="table-responsive">

                        <form class="form-inline" method="GET">
                            
                            <input type="hidden" name="type" value="{{$type}}">
                            <div class="col-md-3">
                                <label>Title:</label><br/>
                                <input type="text" name="title" class="form-control admin_input1" value="{{$old_title}}">
                            </div>

                            <div class="col-md-2">
                                <label>Category:</label><br/>
                                <select name="category_id" class="form-control admin_input1">
                                    <option value="">--Select--</option>
                                <?php
                                if(!empty($categories) && count($categories) > 0){

                                    foreach($categories as $cat){

                                        $selected = '';

                                        if( $cat->id == old('category_id', $old_category_id) ){
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
                            </div>

                             <div class="col-md-2">
                                <label>Status:</label><br/>
                                <select name="status" class="form-control admin_input1">
                                    <option value="">--Select--</option>
                                    <option value="1" {{ ($old_status == '1')?'selected':'' }}>Active</option>
                                    <option value="0" {{ ($old_status == '0')?'selected':'' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>Featured:</label><br/>
                                <select name="featured" class="form-control admin_input1">
                                    <option value="">--Select--</option>
                                    <option value="1" {{ ($old_featured == '1')?'selected':'' }}>Active</option>
                                    <option value="0" {{ ($old_featured == '0')?'selected':'' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label>From Date:</label><br/>
                                <input type="text" name="from" class="form-control admin_input1 to_date" value="{{$old_from}}" autocomplete="off" >
                            </div>

                            <div class="col-md-2">
                                <label>To Date:</label><br/>
                                <input type="text" name="to" class="form-control admin_input1 from_date" value="{{$old_to}}" autocomplete="off" >
                            </div>

                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success btn1search">Search</button>

                                <a href="{{url($routeName.'/blogs?type='.$type)}}" class="btn resetbtn btn-primary btn1search" >Reset</a>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>

            @include('snippets.errors')
            @include('snippets.flash')

            <div class="ajax_msg"></div>

            <?php
            if(!empty($blogs) && count($blogs) > 0){
                ?>

                <div class="table-responsive">

                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Featured</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($blogs as $blog){
                            $content = CustomHelper::wordsLimit($blog->content,35);

                            $blog_category = $blog->Category;

                            $blogMainCategory = '';
                            $category_name = (isset($blog_category->name))?$blog_category->name:'';

                            $blogCategories = isset($blog->blogCategories) ? $blog->blogCategories:'';
                            $blogMainCategories = isset($blog->blogMainCategories) ? $blog->blogMainCategories:'';

                            //pr($blogCategories);

                            if(isset($blogMainCategories[0]) && count($blogMainCategories[0]) > 0){
                                $blogMainCategory = $blogMainCategories[0];
                            }
                            $selected_cat_name = [];

                            /*if(!empty($blogMainCategories) && count($blogMainCategories) > 0){
                                foreach ($blogMainCategories as $mainCat){
                                    $selected_cat_name = CustomHelper::CategoryBreadcrumb($mainCat, $routeName.'/categories?back_url='.$back_url, '');
                                }
                            }*/
                            $category_hierarchy = '';
                            if(!empty($blogCategories) && count($blogCategories) > 0){
                                foreach($blogCategories as $cat){
                                    $CategoryBreadcrumb = CustomHelper::CategoryBreadcrumb($cat, '', '');

                                    $category_hierarchy = str_replace('<i aria-hidden="true" class="fa fa-angle-double-right"></i>', "&gt;", $CategoryBreadcrumb);
                                    $category_hierarchy = strip_tags($category_hierarchy);
                                }
                        }

                            $CategoryBreadcrumb = CustomHelper::CategoryBreadcrumb($blogMainCategory, $routeName.'/categories?back_url='.$back_url, '');
                            
                            ?>
                        
                            <tr>
                                <td><?php echo $blog->title; ?></td>
                                
                                
                                <td><?php
                             echo $category_hierarchy; 

                            if(!empty($selected_cat_name)){
                               echo implode(',', $selected_cat_name);
                            }

                            ?></td>
                                <td>
                                    <input type="checkbox" name="featured" class="is_featured" data-id="{{$blog->id}}" value="1" <?php if($blog->featured == 1){ echo 'checked';} ?>>
                                </td>
                                <td>{{ $blog->sort_order }}</td>
                                <td>{{ CustomHelper::getStatusStr($blog->status) }}</td>

                                <td>
                                    <a href="{{ route($routeName.'.blogs.edit', [$blog->id,'back_url'=>$back_url,'type'=>$type]) }}" title="Edit"><i class="fas fa-edit"></i></a>

                                    <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$blog->id}}" title="Delete"><i class="fas fa-trash-alt"></i></i></a>
                                
                                    <form method="POST" action="{{ route($routeName.'.blogs.delete', $blog->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove?');" id="delete-form-{{$blog->id}}">
                                        {{ csrf_field() }}
                                        {{ method_field('POST') }}
                                        <input type="hidden" name="id" value="<?php echo $blog->id; ?>">
                                        <input type="hidden" name="type" value="{{$type}}">

                                    </form>

                                     <!-- <a href="{{ route($routeName.'.images.upload',['tid'=>$blog->id,'tbl'=>'blogs']) }}" target="_blank"><i class="fas fa-image" title="Add Images"></i></a>

                                     <a  href="{{ route($routeName.'.videos.add',['vid'=>$blog->id,'tbl'=>'blogs']) }}" target="_blank"><i class="fa fa-video" title="Add Videos"></i></a> -->

                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                 {{ $blogs->appends(request()->query())->links() }}

            
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no Records at the present.</div>
            <?php
        }
            ?>

        </div>

    </div>


@slot('bottomBlock')

<link rel="stylesheet" href="{{ url('public/css/jquery-ui.css') }}">
<script type="text/javascript" src="{{ url('public/js/jquery-ui.js') }}"></script>

<script type="text/javaScript">
    $('.sbmtDelForm').click(function(){
        var id = $(this).attr('id');
        $("#delete-form-"+id).submit();
    });

    $( function() {
        $( ".to_date, .from_date" ).datepicker({
            'dateFormat':'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
        });
    });

    

    $(".is_featured").click(function(){

        var current_sel = $(this);

         if(this.checked) {
            featured_true = 1;
         }
         else{
            featured_true = 0;
         }
        //alert(featured_true); return false;

        var blog_id = $(this).attr('data-id');

        conf = confirm("Are you sure to Featured this blog?");

        if(conf){

            var _token = '{{ csrf_token() }}';
            var type = '{{$type}}';

            $.ajax({
                url: "{{ route($routeName.'.blogs.ajax_change_featured') }}",
                type: "POST",
                data: {blog_id:blog_id, type:type,featured_true:featured_true},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                cache: false,
                beforeSend:function(){
                   $(".ajax_msg").html("");
                },
                success: function(resp){
                    if(resp.success){
                        $(".ajax_msg").html(resp.msg);
                        //current_sel.parent('.image_box').remove();
                    }
                    else{
                        $(".ajax_msg").html(resp.msg);
                    }
                    
                }
            });
        }

    });
</script>

@endslot

@endcomponent