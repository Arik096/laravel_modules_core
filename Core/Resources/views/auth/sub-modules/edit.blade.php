@extends('core::layouts.app')
@section('title','Component Edit')
@section('top_script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px;
            font-size: 16px !important;
            color: #666666 !important;
        }
    </style>
@endsection
@section('content')
    {{Form::open(['route'=>['core.components.update',$prefix,$data->id],'method'=>'PUT','files'=>true])}}
    <div class="isocial-body-section box-bottom-shadow">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <nav aria-label="breadcrumb" style="margin-top: 17px;">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('core.components.index',$prefix)}}" class="text-d-none"><b>Components</b></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Components</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6 text-end" style="margin-top: 12px; margin-bottom: 13px;">
                    <button type="submit" class="btn-create-module"><i class="fa fa-save"></i> Save</button>
                    <a href="{{route('core.components.index',$prefix)}}" class="discard-button"><b>DISCARD</b></a>
                </div>

            </div>
        </div>
    </div>



    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom" style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">
            <div class="container">
                <div class="row">
                    <div class="col" style="margin-top: 40px; padding-left: 40px;">

                        <div class="col-md-8">

                                <div class="mb-5">
                                    <label class="form-label">Component Name</label>
                                    <input type="text" name="title" value="{{$data->title}}" class="form-control i-f-d" placeholder="Write your submodule name">
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Nick Name</label>
                                    <input type="text" name="nick_name" value="{{$data->nick_name}}" class="form-control i-f-d" placeholder="Write Component Nick Name">
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Action Name</label>
                                    <input type="text" name="action" value="{{$data->action}}" class="form-control i-f-d" placeholder="Write your action name">
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Action URL</label>
                                    @php
                                        $arr = array('url'=>'URL','route'=>"Route");
                                    @endphp
                                    <select name="action_type" class="form-control isocial-form-design">
                                        @foreach($arr as $k => $act)
                                            @if($data->action_type == $k)
                                                <option value="{{$k}}" selected>{{$act}}</option>
                                            @else
                                                <option value="{{$k}}">{{$act}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>


                                <div class="mb-5">
                                    <label class="form-label">Modules</label>
                                    <select name="module_id"  class="form-select i-f-d js-example-basic-single">
                                        <option value="">Choose</option>
                                        @foreach($modules as $module)
                                            @if($data->module_id == $module->id)
                                                <option value="{{$module->id}}" selected>{{$module->title}}</option>
                                            @else
                                                <option value="{{$module->id}}">{{$module->title}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>



                                <div class="mb-5">
                                    <label class="form-label">Component Icon</label>
                                    <input type="text" name="icons" value="{{$data->icons}}" class="form-control i-f-d" placeholder="Font Awesome Icon">
                                </div>


                        </div>

                    </div>
                    <div class="col" style="margin-top: 40px; padding-right: 40px;">

                        <div class="col-md-12">

                            <div class="mb-5">
                                <label class="form-label">Background Color One</label>
                                <input type="text" name="bg_color_one" value="{{$data->bg_color_one}}" class="form-control i-f-d" placeholder="Background Color One">
                            </div>

                            <div class="mb-5">
                                <label class="form-label">Background Color One</label>
                                <input type="text" name="bg_color_two" value="{{$data->bg_color_two}}" class="form-control i-f-d" placeholder="Background Color Two">
                            </div>


                            <div class="mb-3">
                                <label for="exampleFormControlTextarea1" class="form-label">Write Details about this Component</label>
                                <textarea name="comments" class="form-control i-f-d- color-gray" id="exampleFormControlTextarea1" rows="10">{{$data->comments}}</textarea>
                            </div>


                            <div class="input-group mb-3">
                                <input type="file" name="images" id="icon" style="display: none"/>
                                <label for="icon"  class="btn btn-outline-warning btn-b-dashed" style="margin-right: 20px !important;margin-top: 0px;"><span style="color: #9072ba;">Upload Custom Icon</span></label>
                                @if($data->upload_icon != "")
                                    <img id="changeImageDesktop" src="{{asset('public/uploads/submodule-icon/'.$data->upload_icon)}}" height="50px" width="50px;"/>
                                @else
                                    <img id="changeImageDesktop" src="{{asset('Modules/Core/Public/Uploads/no-image.png')}}" height="50px" width="50px;"/>
                                @endif
                            </div>


                            <div class="input-group mb-3">
                                <input type="file" name="hover_images" id="icon_hover" style="display: none"/>
                                <label for="icon_hover"  class="btn btn-outline-warning btn-b-dashed" style="margin-right: 20px !important;margin-top: 0px;"><span style="color: #9072ba;">Upload Hover Custom Icon</span></label>
                                @if($data->icon_hover != "")
                                    <img id="changeImageDesktop" src="{{asset('public/uploads/submodule-icon/'.$data->icon_hover)}}" height="50px" width="50px;"/>
                                @else
                                    <img id="changeImageDesktop" src="{{asset('Modules/Core/Public/Uploads/no-image.png')}}" height="50px" width="50px;"/>
                                @endif
                            </div>


                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-1" style="margin-top: 30px;"></div>
            <div class="col-md-6" style="margin-top: 30px;"></div>

        </section>
    </div>

    {{Form::close()}}
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
