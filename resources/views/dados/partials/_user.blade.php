
@if($cliente)
    <div class="card card-secondary card-outline widget-user-2">

        <div class="widget-user-header d-flex justify-content-center">
            <div class="widget-user-image">
                <img src="{{ Storage::url($cliente->logo_url) }}" alt="Logo Cliente" style="width: 400px; height: 400px; object-fit: cover; border-radius: 8px;">
            </div>
        </div>

        <div class="card-footer text-right">
            <a href="#" class="btn btn-sm btn-tool">
                <i class="fa-solid fa-circle-info"></i>
            </a>
        </div>
        
    </div>
@endif

