<script>
    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
        toastr.success("{{ session('success') }}", "Éxito", { timeOut: 5000 });
        @endif

        @if(session('error'))
        toastr.error("{{ session('error') }}", "Error", { timeOut: 5000 });
        @endif

        @if(session('warning'))
        toastr.warning("{{ session('warning') }}", "Advertencia", { timeOut: 5000 });
        @endif

        @if(session('info'))
        toastr.info("{{ session('info') }}", "Información", { timeOut: 5000 });
        @endif
    });
</script>
