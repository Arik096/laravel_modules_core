<div>
    <div class="mb-5">
        <label class="form-label">Modules</label>
        <select id="module_id_select2" name="module_id"  class="form-select i-f-d js-example-basic-single">
            <option value="">Choose</option>
            @foreach($modules as $module)
                @if($module_id == $module->id || $module->id == $Moduleid)
                    <option value="{{$module->id}}" selected>{{$module->title}}</option>
                @else
                    <option value="{{$module->id}}">{{$module->title}}</option>
                @endif
            @endforeach
        </select>
    </div>


    <div class="mb-5">
        <label class="form-label">Components</label>
        <select id="module_id_select1" name="sub_module_id"  class="form-select i-f-d js-example-basic-single">
            <option value="">Choose</option>
            @foreach($submodules as $sub)
                @if($submodule_id == $sub->id)
                    <option value="{{$sub->id}}" selected>{{$sub->title}}</option>
                @else
                    <option value="{{$sub->id}}">{{$sub->title}}</option>
                @endif
            @endforeach
        </select>
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



