<x-filament-widgets::widget>
    <x-filament::card>
        <div id="calendar"></div>
    </x-filament::card>

    <script type="module">
        import { Calendar } from 'fullcalendar';

        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            let calendar = new Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                editable: true,
                selectable: true,

                // Ambil event yang sudah kamu generate dari model Order
                events: @json($this->getSchedules()),

                console.log(@json($this->getSchedules()));

                // Klik tanggal → buat order baru
                dateClick(info) {
                    window.location.href = '/admin/orders/create?date=' + info.dateStr;
                },

                // Klik event → edit order
                eventClick(info) {
                    window.location.href = '/admin/orders/' + info.event.id + '/edit';
                }
            });

            calendar.render();
        });

    </script>
</x-filament-widgets::widget>
