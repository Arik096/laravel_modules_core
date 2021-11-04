@extends('core::layouts.app')
@section('title','Modules Create')
@section('top_script')
    <style>
        #changeImageDesktop{
            display: block;
        }
    </style>
@endsection
@section('content')

    {{Form::open(['route'=>['core.modules.store',$prefix],'method'=>'POST','files'=>true])}}
    <div class="isocial-body-section box-bottom-shadow">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-6">
                    <nav aria-label="breadcrumb" style="margin-top: 17px;">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('core.modules.index',$prefix)}}" class="text-d-none"><b>Module</b></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Module</li>
                        </ol>
                    </nav>
                </div>

                <div class="col-md-6 text-end" style="margin-top: 12px; margin-bottom: 13px;">
                    <button type="submit" class="btn-create-module"><i class="fa fa-save"></i> Save</button>
                    <a href="{{route('core.modules.index',$prefix)}}" class="discard-button"><b>DISCARD</b></a>
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
                            <form>
                                <div class="mb-5">
                                    <label class="form-label">Module Name</label>
                                    <input type="text" name="title" class="form-control i-f-d" placeholder="Write your module name">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Action Name</label>
                                    <input type="text" name="action" class="form-control i-f-d" placeholder="Write your action name">
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Action URL</label>
                                    <select name="action_type" class="form-select i-f-d" aria-label="Default select example">
                                        <option value="url">Url</option>
                                        <option value="route">Route</option>
                                    </select>
                                </div>

                                <div class="mb-5">
                                    <label class="form-label">Module Icon</label>
                                    <input type="text" name="icons" class="form-control i-f-d" placeholder="Font Awesome Icon">
                                </div>


                                <div class="input-group mb-3">
                                    <input type="file" name="images" id="icon" style="display: none"/>
                                    <label for="icon"  class="btn btn-outline-warning btn-b-dashed" style="margin-right: 20px !important;margin-top: 0px;"><span style="color: #9072ba;">Upload Custome Icon</span></label>
                                    <img id="changeImageDesktop" src="{{asset('Modules/Core/Public/Uploads/no-image.png')}}" height="50px" width="50px;"/>
                                </div>
                            </form>
                        </div>

                    </div>
                    <div class="col" style="margin-top: 40px; padding-right: 40px;">

                        <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="exampleFormControlTextarea1" class="form-label">Write Details about this Module</label>
                                    <textarea name="comments" class="form-control i-f-d- color-gray" id="exampleFormControlTextarea1" rows="10"></textarea>
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
@endsection

@section('bottom_script')
<script>
    $('#icon').change(function () {
        $("#changeImageDesktop").attr('src',window.URL.createObjectURL(this.files[0]))
    });
</script>
@endsection
