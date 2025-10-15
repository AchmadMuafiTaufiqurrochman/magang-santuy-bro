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
                events: @json($this->getSchedules()),

                dateClick(info) {
                    // Arahkan ke Create
                    window.location.href = '/admin/schedules/create?date=' + info.dateStr;
                },
                eventClick(info) {
                    // Arahkan ke Edit
                    window.location.href = '/admin/schedules/' + info.event.id + '/edit';
                }
            });

            calendar.render();
        });
    </script>
</x-filament-widgets::widget>
