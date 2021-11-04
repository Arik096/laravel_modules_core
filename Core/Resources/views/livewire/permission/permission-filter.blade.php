<div>

    <div class="row" style="padding: 0px 20px;box-sizing: border-box">
        <div class="col-md-4">
            <input name="search" type="text" class="search" placeholder="Search">
        </div>

        <div class="col-md-3">
            <select id="module_id_select2" name="module_id" class="form-control js-example-basic-single isocial-form-design">
                <option value="">Choose</option>
                @foreach($modules as $module)
                    @if($Moduleid == $module->id)
                        <option value="{{$module->id}}" selected>{{$module->title}}</option>
                    @else
                        <option value="{{$module->id}}">{{$module->title}}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select id="module_id_select1" name="sub_module_id" class="form-control js-example-basic-single isocial-form-design">
                <option value="">Choose</option>
                @foreach($submodules as $sub)
                    <option value="{{$sub->id}}">{{$sub->title}}</option>
                @endforeach
            </select>
        </div>


        <div class="col-md-2">
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
        </div>
    </div>


  <script>
            $(document).ready(function() {
                $('#module_id_select2').select2();
                $('#module_id_select2').on('change', function (e) {
                    var selectedData = $('#module_id_select2').select2("val");
                    @this.set('Moduleid', selectedData);
                });
            });

            document.addEventListener("livewire:load", () => {
                Livewire.hook('message.processed', (message, component) => {
                    $('#module_id_select2').select2();
                    $('#module_id_select1').select2();
                });
            });
  </script>


</div>



