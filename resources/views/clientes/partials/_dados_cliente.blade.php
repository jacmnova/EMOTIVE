<div class="card card-default">
    <div class="card-header">
        <h3 class="card-title"><i class="fa-solid fa-building-user" style="margin-right: 5px;"></i> Detalhes do Cliente</h3>
        <div class="card-tools">

            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fa-solid fa-plus-minus"></i>
            </button>
        </div>
    </div>
   
    <div class="card-body" style="display: block;">
        <div class="row">
            <div class="col-md-8">

                <h4 style="margin: 0;">{{ $dadosCliente->nome_fantasia }}</h4>
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                @if($dadosCliente->tipo == 'cpf')
                                    <th style="width:30%">{{strtoupper($dadosCliente->tipo)}}:</th>
                                    <td>{{ $dadosCliente->formatted_cpf}}</td>
                                @elseif($dadosCliente->tipo == 'cnpj')
                                    <th style="width:30%">{{strtoupper($dadosCliente->tipo)}}:</th>
                                    <td>{{ $dadosCliente->formatted_cnpj}}</td>
                                @else
                                <th style="width:30%">{{strtoupper($dadosCliente->tipo)}}:</th>
                                <td>{{ $dadosCliente->reg}}</td>
                                @endif
                            </tr>
        
                            <tr>
                                <th>Razão Social:</th>
                                <td>{{$dadosCliente->razao_social}}</td>
                            </tr>
        
                            <tr>
                                <th>Contato:</th>
                                <td>{{$dadosCliente->contato}}</td>
                            </tr>
        
                            <tr>
                                <th>E-mail:</th>
                                <td>{{$dadosCliente->email}}</td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td>{{$dadosCliente->telefone}}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>@if($dadosCliente->ativo == 1)
                                        Ativo
                                    @else
                                        Inativo
                                    @endif
                                </td>
                            </tr>
        
                        </tbody>
                    </table>
                </div>
            
            </div>
        
            <div class="col-md-3 text-center">
                <img src="{{ Storage::url($dadosCliente->logo_url) }}" class="img-fluid img-thumbnail mx-auto d-block" alt="Logo" style="width: 250px;" />
            </div>
  
        </div>
    </div>
    
    <div class="card-footer" style="display: block;">

    </div>
</div>