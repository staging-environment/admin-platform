<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulario Filtro -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form wire:submit="generateReport" class="space-y-6">
                    {{ $this->form }}
                    <div class="mt-4 flex justify-end">
                        <x-filament::button type="submit" color="primary">
                            Generar Informe
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Script de Chart.js cargado directamente -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Canvas Gráfica -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" 
             wire:ignore
             x-data="{ 
                 chart: null,
                 init() {
                     window.addEventListener('update-chart', (event) => {
                         let data = event.detail.data || (event.detail[0] ? event.detail[0].data : null) || event.detail[0] || event.detail;
                         if (!data) return;
                         
                         if (this.chart) {
                             this.chart.destroy();
                         }
                         
                         const ctx = document.getElementById('reportChart').getContext('2d');
                         this.chart = new Chart(ctx, {
                             type: data.type,
                             data: {
                                 labels: data.labels,
                                 datasets: data.datasets
                             },
                             options: Object.assign({
                                 responsive: true,
                                 maintainAspectRatio: false,
                                 plugins: {
                                     legend: { position: 'bottom' }
                                 }
                             }, data.options || {})
                         });
                         
                         this.$el.style.display = 'block';
                     });
                 }
             }"
             style="display: none;"
        >
            <div class="p-6">
                <div style="height: 400px; position: relative;">
                    <canvas id="reportChart"></canvas>
                </div>
            </div>
        </div>


    </div>
</x-filament-panels::page>
