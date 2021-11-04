<div class="modal fade" id="bt5DeleteModal" tabindex="-1" aria-labelledby="bt5DeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bt5DeleteModalLabel" style="text-align: center; margin: 0 auto;">
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    <i class="fa fa-exclamation-triangle text-warning"></i> Warning
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mt-3">
                    Do you want to <span class="text-danger"><b>delete</b></span> this <span id="module-text-1">module</span>? <br>
                    Please confirm you want to <span class="text-danger"><b>delete</b></span> it. <br>
                    If you <span class="text-danger"><b>delete</b></span> it your <span id="module-text-2">module</span> can not seen anyone.
                </p>
            </div>
            <div class="modal-footer text-center">
                <div class="col">
                    <button type="button" class="btn-round-skip" data-bs-dismiss="modal" aria-label="Close"> &nbsp; &nbsp; SKIP &nbsp; &nbsp; </button>
                </div>

                <div class="col">
                    <form id="bot-5-delete-model"  method="post">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn-round-delete"> &nbsp; &nbsp; DELETE &nbsp; &nbsp; </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>


<script>
    var myModalEl = document.getElementById('bt5DeleteModal')
    myModalEl.addEventListener('show.bs.modal', function (event) {
        let button = $(event.relatedTarget)
        let url = button.data('url');
        $("#bot-5-delete-model").attr('action',url)
        $("#module-text-1").text(button.data('altxt'))
        $("#module-text-2").text(button.data('altxt'))
    })
</script>
