@extends('core::layouts.app')
@section('title','Permissions List')
@section('top_script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
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

    <div class="form-top-card">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p style="margin: 10px 0px;font-size: 14px;">
                        <a href="{{route('core.permissions.index',$prefix)}}" style="text-decoration: none"><span
                                style="font-size: 16px;color: #8E73BE"> <i class="fas fa-sliders-h"></i> Permissions List</span></a>
                        <a href="{{route('core.permissions.create',$prefix)}}">
                            <button class="btn-create-module float-right"><i class="fa fa-plus"></i> Create</button>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>



    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom"
                 style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">
            <div class="row g-0 m">
                <div class="col-md-10 m-t-20">
                    {{Form::open(['route'=>['core.permissions.filter',$prefix],'method'=>'GET'])}}
                    @livewire('core::permission.permission-filter')
                    {{Form::close()}}
                </div>

                <div class="col-md-2 m-t-20">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn-export p-t-3 p-b-3"><i class="fa fa-file-import"></i> &nbsp;
                            Export
                        </button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                    <div class="col-md-12 text-end m-t-10 m-b-mi-6">
                        <button type="button" class="btn-import mx-3- p-t-3 p-b-3"><i class="fa fa-download"></i> &nbsp;
                            Import
                        </button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-responsive table-striped f-s-12">
                    <thead>
                    <tr>
                        <th scope="col">
                            <input class="form-check-input mt-0 ckbox-1-4" type="checkbox" value=""
                                   aria-label="Checkbox for following text input">
                            Serial No
                        </th>
                        <th scope="col">Create Date</th>
                        <th scope="col">Permisson Name</th>
                        <th scope="col">Action</th>
                        <th scope="col" style="white-space: nowrap">Module Name</th>
                        <th scope="col" style="white-space: nowrap">Component Name</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(count($datas) > 0)

                        @foreach($datas as $k => $data)

                            <tr class="t-row">
                                <td>
                                    <b>{{$datas->firstItem() + $k}}</b>
                                </td>
                                <td>{{date('jS F, Y', strtotime($data->created_at))}}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->action}}</td>
                                <td>
                                    @if(!is_null($data->module))
                                        {{$data->module->title}}
                                    @endif
                                </td>

                                <td>
                                    @if(!is_null($data->submodule))
                                        {{$data->submodule->title}}
                                    @endif
                                </td>

                                <td>
                                    <div style="width: 220px;">

                                        <a href="{{url('account/'.$prefix.'/settings/permissions/'.$data->id.'/edit')}}">
                                            <button type="button" class="btn-edit">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                        </a>

                                        <button type="submit" class="btn-delete" data-bs-toggle="modal"
                                                data-bs-target="#bt5DeleteModal"
                                                data-url="{{url('account/'.$prefix.'/settings/permissions/'.$data->id)}}"
                                                data-altxt="Permission">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                    @else
                        <tr>
                            <td colspan="6" align="center">Data Not Found</td>
                        </tr>
                    @endif
                    </tbody>
                </table>




            </div>
        </section>
        <br/>
        {{$datas->links()}}
        <br/>

    </div>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function () {
            $('.js-example-basic-single').select2();
        });
    </script>

@endsection
