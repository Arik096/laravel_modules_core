@extends('core::layouts.app')
@section('title','Role List')
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

    <div class="form-top-card">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <p style="margin: 10px 0px;font-size: 14px;">
                        <span style="font-size: 16px;color: #8E73BE"> <i class="fas fa-sliders-h"></i> Roles List</span>
                        <a href="{{route('core.roles.create',$prefix)}}"> <button class="btn-create-module float-right"><i class="fa fa-plus"></i> Create</button></a>
                    </p>
                </div>
            </div>
        </div>
    </div>



    <div class="container-xxl" style="margin-top: 20px;">
        <section class="container-xxl isocial-body-section section-table-border-custom" style="border-top: none; border-bottom: none; padding-right: 0px; padding-left: 0px;">
            <div class="row g-0 m">
                <div class="col-md-4"></div>
                <div class="col-md-4 m-t-20">
                    {{Form::open(['route'=>['core.roles.filter',$prefix],'method'=>'GET'])}}
                    <div class="input-group mb-3"> &nbsp;  &nbsp;
                        <input type="text" name="search" class="form-control" style="border-radius: 20px 0px 0px 20px; background-color: #e9e6ee;">
                        <button type="submit" class="input-group-text btn-search" id="basic-addon2"><i class="fa fa-search fa-lg" style="font-size: 15px;"></i> &nbsp; Search</button> &nbsp;  &nbsp;
                    </div>
                    {{Form::close()}}
                </div>

                <div class="col-md-2 m-t-20">
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn-export p-t-3 p-b-3"><i class="fa fa-file-import"></i> &nbsp; Export</button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                    <div class="col-md-12 text-end m-t-10 m-b-mi-6">
                        <button type="button" class="btn-import mx-3- p-t-3 p-b-3"><i class="fa fa-download"></i> &nbsp; Import</button> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    </div>
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-responsive table-striped f-s-12">
                    <thead>
                    <tr>
                        <th scope="col">
                            Serial No
                        </th>
                        <th scope="col">Create Date</th>
                        <th scope="col">Role Name</th>
                        <th scope="col" style="white-space: nowrap">Flag</th>
                        <th scope="col">Comments</th>
                        <th scope="col" style="white-space: nowrap"></th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(count($datas) > 0)

                        @foreach($datas as $k => $data)
                            <tr class="t-row">
                                <td>
                                    <b style="padding-left: 20px;">{{$datas->firstItem() + $k}}</b>
                                </td>
                                <td>{{date('jS F, Y', strtotime($data->created_at))}}</td>
                                <td>{{$data->name}}</td>
                                <td>{{$data->flag}}</td>
                                <td>{{$data->comments}}</td>
                                <td style="width: 220px">
                                        <a href="{{url('account/'.$prefix.'/settings/roles/'.$data->id.'/edit')}}"><button type="button" class="btn-edit"><i class="fa fa-edit"></i> Edit</button></a>

                                        <button type="submit" class="btn-delete" data-bs-toggle="modal" data-bs-target="#bt5DeleteModal"
                                                data-url="{{url('account/'.$prefix.'/settings/roles/'.$data->id)}}"
                                                data-altxt="Role"
                                        ><i class="fa fa-trash"></i> Delete</button>
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
    </div>

    <script>
        // In your Javascript (external .js resource or <script> tag)
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>

@endsection
