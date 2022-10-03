<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Send Email</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                    <label>Issue</label>
                </div>
                <div class="col-md-12">
                    <input type="text" class="form-control" maxlength="20" placeholder="Organization" name="issue" id="issue">
                </div>
                <div class="col-md-12">
                    <label>Content</label>
                </div>
                <div class="col-md-12">
                    <textarea name="content" maxlength="70" id="content" cols="3" rows="3" class="form-control"></textarea>
                </div>
                <div class="col-md-12">
                    <label>File</label>
                </div>
                <div class="col-md-12">
                     <input class="form-control" name="filea" id="filea" type="file" accept="application/pdf, image/gif, image/jpeg" />
                </div>
                <div class="col-md-12">
                    &nbsp;
                </div>
                <div class="col-md-12" style="text-align: center;">
                    <button class="btn btn-primary" type="button" onclick="send_email('create')"> <i class="ficon" data-feather="save"> </i> Save </button>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <!-- button type="button" class="btn btn-primary">Save changes</button> -->
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
</div>
<script>
    
    function send_email() {
        Swal.fire({
            title: '多Do you want to send mail to {{$user->name}} ?',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
               var data = new FormData()
               const input = document.getElementById('filea');

                data.append('file', input.files[0])
                data.append('content',$('#content').val())
                return fetch(`{{route('mail.send',['id'=>$user->id])}}`,{
                      headers: {
                      'X-CSRF-TOKEN': $('input[name=_token]').val()
                       },
                      method: 'POST',
                      body: data
                    })
                    .then(response => {
                        //console.log(response);
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire("Good!", "Correct!", "success");
            }

        });
    }
</script>