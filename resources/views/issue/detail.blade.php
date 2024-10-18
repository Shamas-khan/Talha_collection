@extends('layout.layout')
@section('content')
    <div class="right_col" role="main" style="min-height: 724px;">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    @if ($issueMaterials)
                        <h3 id="head" data-cid="{{ $issueMaterials->issue_material_id }}">
                            {{ $issueMaterials->vendor_name }} 
                            <span style="font-size: 16px;color:black;">Issue Product: 
                                <span style="color: #73879c;">{{ $issueMaterials->product_name }}</span>
                                ,</span>


                            <span style="font-size: 16px ;color:black;"> Production Cost: 
                             <span style="color: #73879c;">   {{ $issueMaterials->unit_cost }}
                            </span>
                             </span>
                        </h3>
                    @endif
                </div>


            </div>

            <div class="clearfix"></div>

            <div class="row">

                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                          
                            <h2>
                                <button type="button" class="btn text-white bg-primary p-2" data-toggle="modal"
                                    data-target=".bs-example-modal-lg">Issue Material</button>
                            </h2>
                            

                            {{-- model --}}
                            <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" style="display: none;"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">Issue Material</h4>
                                            <button type="button" class="close" data-dismiss="modal"><span
                                                    aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        <form id="issueeezero" action="{{ route('issue.material') }}" method="post" class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left">
    @csrf
    <input type="hidden" name="issue_material_id" value="{{ $issueMaterials->issue_material_id }}" class="form-control">
    <div id="input-fields-container" class="align-items-end w-100 ">
        <div class="col-5 float-left">
            <label for="raw-material-0" class="form-label">Raw Material</label>
            <select class="form-control rawMaterialId select22" title="0" name="raw_material_id[]" id="issue-raw_material_id-0">
                <option value="default" selected disabled>Select</option>
                @if ($RawMaterial)
                    @foreach ($RawMaterial as $d)
                        <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('raw_material_id.0')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="col-5 float-left">
            <label for="quantity-0" class="form-label"> Issue Quantity <Small>In inches</small></label>
            <input type="number" title="0" name="quantity[]" class="form-control qunaityfields" id="qty-0" aria-describedby="quantityHelp">
            <span title="0" class="text-center quaniitybyserver text-success" id="issue-av-qty-0"></span>
            @error('quantity.0')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        
        </div>
        <div class="mr-3 col-1  float-left " style="margin-top: 4%">
            <p class="plus-custom mcustom"><i class="fa fa-plus"></i></p>
        </div>
    </div>
    <div class="modal-footer w-100 mt-4">
        <p id="errr"></p>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submit-btn"  type="submit" class="btn btn-primary ">Submit</button>
    </div>
</form>

                                        
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let counter = 1;

        function addNewFields() {
            let newElement = document.createElement('div');
            newElement.classList.add('align-items-end', 'w-100');
            newElement.innerHTML = `
                <div class="col-5 float-left">
                    <label for="raw-material-${counter}" class="form-label">Raw Material</label>
                    <select class="form-control rawMaterialId select22" name="raw_material_id[]" id="issue-raw_material_id-${counter}" title="${counter}">
                        <option value="default" selected disabled>Select</option>
                        @if ($RawMaterial)
                            @foreach ($RawMaterial as $d)
                                <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-5 float-left">
                    <label for="quantity-${counter}" class="form-label">Quantity<Small>In inches</small></label>
                    <input type="number" title="${counter}" name="quantity[]" class="form-control qunaityfields" id="quantity-${counter}" aria-describedby="quantityHelp">
                    <span title="${counter}" class="text-center quaniitybyserver text-success" id="issue-av-qty-${counter}"></span>
                </div>
                <div class=" mr-3  float-left" style="margin-top: 4%">
                    <p class="plus-custom mcustom"><i class="fa fa-plus"></i></p>
                </div>
                <div class=" float-left" style="margin-top: 4%">
                    <p class="minus-custom mcustom"><i class="fa fa-minus"></i></p>
                </div>`;
            counter++;

            let container = document.getElementById('input-fields-container');
            container.appendChild(newElement);

            newElement.querySelector('.plus-custom').addEventListener('click', () => {
                addNewFields();
                newElement.querySelector('.plus-custom').parentElement.style.display = 'none';
            });

            newElement.querySelector('.minus-custom').addEventListener('click', () => {
                container.removeChild(newElement);
                const buttons = document.querySelectorAll('.plus-custom');
                if (buttons.length > 0) {
                    buttons[buttons.length - 1].parentElement.style.display = 'block';
                }
            });
            $(".select22").each((_i, e) => {
        var $e = $(e);
        $e.select2({
        theme: 'bootstrap4',
          width: '100%' ,
          
          dropdownParent: $e.parent()
        });
      })
        }

        document.querySelectorAll('.plus-custom').forEach((button) => {
            button.addEventListener('click', () => {
                addNewFields();
                button.parentElement.style.display = 'none';
            });
        });
    });

    
</script>
                                         

                                        </div>
                                    </div>

                                </div>
                            </div>
                       


                        {{-- end model  --}}


                        <h2>
                                <button type="button" class="btn text-white bg-primary p-2" data-toggle="modal"
                                    data-target=".bs-example-modal-lgg">Return Issue Material</button>
                            </h2>
                            {{-- model --}}
                            <div class="modal fade bs-example-modal-lgg" tabindex="-1" role="dialog" style="display: none;"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">Return Issue Material</h4>
                                            <button type="button" class="close" data-dismiss="modal"><span
                                                    aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                        <form id="issueeezero" action="" method="post" class="align-items-center w-100 flex-wrap d-flex form-horizontal form-label-left">
    @csrf
    <input type="hidden" name="issue_material_id" value="{{ $issueMaterials->issue_material_id }}" class="form-control">
    <div id="input-fields-containers" class="align-items-end w-100 ">
        <div class="col-5 float-left">
            <label for="raw-material-0" class="form-label">Raw Material</label>
            <select class="form-control select22" title="0" name="raw_id[]" id="issue-raw_aa">
                <option value="default" selected disabled>Select</option>
                @if ($issuewalarawmaterial)
                    @foreach ($issuewalarawmaterial as $d)
                        <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                    @endforeach
                @endif
            </select>
            @error('raw_material_id.0')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        
        <div class="col-5 float-left">
            <label for="quantity-0" class="form-label"> Return Quantity <Small>In inches</small></label>
            <input type="number" title="0" name="qty[]" class="form-control qunaityfields" id="qty-0" aria-describedby="quantityHelp">
           
            @error('quantity.0')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        
        </div>
        <div class="mr-3 col-1  float-left " style="margin-top: 4%">
            <p class="plus-customs mcustom"><i class="fa fa-plus"></i></p>
        </div>
    </div>
    <div class="modal-footer w-100 mt-4">
        <p id="errr"></p>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button id="submit-btn"  type="submit" class="btn btn-primary ">Submit</button>
    </div>
</form>

                                        
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let counter = 1;

        function addNewFields() {
            let newElement = document.createElement('div');
            newElement.classList.add('align-items-end', 'w-100');
            newElement.innerHTML = `
                <div class="col-5 float-left">
                    <label for="raw-material-${counter}" class="form-label">Raw Material</label>
                    <select class="form-control rawMaterialId select22" name="raw_material_id[]" 
                    id="issue-raw_${counter+1000}" title="${counter}">
                        <option value="default" selected disabled>Select</option>
                        @if ($RawMaterial)
                            @foreach ($RawMaterial as $d)
                                <option value="{{ $d->raw_material_id }}">{{ $d->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-5 float-left">
                    <label for="quantity-${counter}" class="form-label">Return Quantity <Small>In inches</small></label>
                    <input type="number" title="${counter}" name="quantity[]" class="form-control "  aria-describedby="quantityHelp">
                    
                </div>
                <div class=" mr-3  float-left" style="margin-top: 4%">
                    <p class="plus-customs mcustom"><i class="fa fa-plus"></i></p>
                </div>
                <div class=" float-left" style="margin-top: 4%">
                    <p class="minus-customs mcustom"><i class="fa fa-minus"></i></p>
                </div>`;
            counter++;

            let container = document.getElementById('input-fields-containers');
            container.appendChild(newElement);

            newElement.querySelector('.plus-customs').addEventListener('click', () => {
                addNewFields();
                newElement.querySelector('.plus-customs').parentElement.style.display = 'none';
            });

            newElement.querySelector('.minus-customs').addEventListener('click', () => {
                container.removeChild(newElement);
                const buttons = document.querySelectorAll('.plus-customs');
                if (buttons.length > 0) {
                    buttons[buttons.length - 1].parentElement.style.display = 'block';
                }
            });
        
        }

        document.querySelectorAll('.plus-customs').forEach((button) => {
            button.addEventListener('click', () => {
                addNewFields();
                button.parentElement.style.display = 'none';
            });
        });
    });

    
</script>
                                         

                                        </div>
                                    </div>

                                </div>
                            </div>
                       


                        {{-- end model  --}}

                        <ul class="nav navbar-right panel_toolbox">
                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </li>


                        </ul>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card-box table-responsive">
                                    <table id="detail" class="table display table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Material Name</th>
                                                <th>Required Quantity</th>
                                                <th>issue Quantity</th>
                                                <th>Remaining Quantity</th>
                                                <th>issue Qty Original  Size</th>
                                                <th>issue Date</th>

                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>

    <?php $file = 'issuelist.js'; ?>
@endsection
