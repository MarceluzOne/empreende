{{--
    Partial: _calendar.blade.php
    Variáveis opcionais recebidas via @include:
      $preselectedDate  (string Y-m-d) — pré-seleciona data no modo edição
      $preselectedStart (string H:i)   — pré-seleciona horário início
      $preselectedEnd   (string H:i)   — pré-seleciona horário fim
--}}
<div
    x-data="bookingCalendar(
        '{{ $preselectedDate  ?? '' }}',
        '{{ $preselectedStart ?? '' }}',
        '{{ $preselectedEnd   ?? '' }}'
    )"
    x-init="init()"
    @resource-changed.window="onResourceChanged($event.detail.value)"
    @dates-updated.window="externalSelectedDates = $event.detail.dates"
    class="border border-gray-200 rounded-xl overflow-hidden bg-white shadow-sm">

    {{-- Cabeçalho do calendário --}}
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
        <button type="button" @click="prevMonth()"
            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 transition text-gray-600">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>
        <span class="font-semibold text-gray-800 text-sm" x-text="monthLabel"></span>
        <button type="button" @click="nextMonth()"
            class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-200 transition text-gray-600">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    </div>

    <div class="p-4">
        {{-- Legenda --}}
        <div class="flex items-center gap-4 mb-3 text-xs text-gray-500">
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-white border border-gray-300 inline-block"></span> Disponível</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-100 border border-orange-300 inline-block"></span> Parcial</span>
            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 border border-red-300 inline-block"></span> Lotado</span>
        </div>

        {{-- Cabeçalhos dos dias da semana --}}
        <div class="grid grid-cols-7 mb-1">
            <template x-for="d in ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb']">
                <div class="text-center text-xs font-bold text-gray-400 py-1" x-text="d"></div>
            </template>
        </div>

        {{-- Células dos dias --}}
        <div class="grid grid-cols-7 gap-1" x-show="!loading">
            <template x-for="(cell, idx) in calDays" :key="idx">
                <div
                    :class="dayClasses(cell)"
                    @click="cell && cell.date && !isPast(cell.date) && !isWeekend(cell.date) && cell.state !== 'full' ? selectDate(cell.date) : null"
                    class="h-9 flex flex-col items-center justify-center rounded-lg text-sm select-none transition-colors relative">
                    <span x-text="cell ? cell.day : ''"></span>
                    <span x-show="cell && cell.state === 'partial'"
                          class="absolute bottom-1 w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                </div>
            </template>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="flex items-center justify-center py-6 text-gray-400 text-sm gap-2">
            <i class="fas fa-spinner fa-spin"></i> Carregando disponibilidade...
        </div>

        {{-- Grade de horários --}}
        <div x-show="selectedDate && !loading" x-cloak class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-bold text-gray-700">
                    Horários —
                    <span x-text="selectedDate ? selectedDate.split('-').reverse().join('/') : ''"></span>
                </p>
                <span class="text-xs text-gray-400 italic">
                    <span x-show="!selectingEnd && !selectedStart">Clique para definir início</span>
                    <span x-show="selectingEnd">Clique para definir fim</span>
                    <span x-show="selectedStart && selectedEnd" class="text-blue-600 font-semibold">
                        <span x-text="selectedStart"></span> → <span x-text="selectedEnd"></span>
                    </span>
                </span>
            </div>

            <div class="flex flex-wrap gap-1">
                <template x-for="slot in timeSlots" :key="slot.time">
                    <button
                        type="button"
                        :disabled="slot.occupied"
                        :class="slotClasses(slot)"
                        @click="!slot.occupied && clickSlot(slot)"
                        class="px-2 py-1 text-xs rounded border font-mono transition-colors min-w-[52px] text-center"
                        x-text="slot.time">
                    </button>
                </template>
            </div>

            <p x-show="selectedStart && selectedEnd" class="mt-2 text-xs text-blue-700 font-semibold">
                ✔ Horário selecionado: <span x-text="selectedStart"></span> às <span x-text="selectedEnd"></span>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingCalendar', (preselectedDate = '', preselectedStart = '', preselectedEnd = '') => ({
        calYear:  new Date().getFullYear(),
        calMonth: new Date().getMonth() + 1,
        bookingsByDate: {},
        loading: false,
        resourceType: 'auditorio',

        selectedDate:  preselectedDate  || '',
        selectedStart: preselectedStart || '',
        selectedEnd:   preselectedEnd   || '',
        selectingEnd:  false,
        timeSlots: [],
        externalSelectedDates: [],

        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                     'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],

        get monthLabel() {
            return `${this.monthNames[this.calMonth - 1]} ${this.calYear}`;
        },

        init() {
            // Lê resource_type atual do select na página
            const sel = document.querySelector('[name="resource_type"]');
            if (sel) this.resourceType = sel.value;

            // Se há data pré-selecionada (modo edição), navega para o mês correto
            if (preselectedDate) {
                const d = new Date(preselectedDate + 'T00:00:00');
                this.calYear  = d.getFullYear();
                this.calMonth = d.getMonth() + 1;
            }

            this.fetchMonth().then(() => {
                if (preselectedDate) this.buildTimeSlots(preselectedDate);
            });
        },

        onResourceChanged(value) {
            this.resourceType = value;
            this.selectedDate  = '';
            this.selectedStart = '';
            this.selectedEnd   = '';
            this.selectingEnd  = false;
            this.timeSlots     = [];
            this.fetchMonth();
        },

        async fetchMonth() {
            this.loading = true;
            try {
                const url = `/bookings/availability?resource_type=${this.resourceType}&year=${this.calYear}&month=${this.calMonth}`;
                const resp = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await resp.json();
                this.bookingsByDate = data.bookings_by_date || {};
            } catch (e) {
                this.bookingsByDate = {};
            }
            this.loading = false;
        },

        prevMonth() {
            if (this.calMonth === 1) { this.calMonth = 12; this.calYear--; }
            else { this.calMonth--; }
            this.fetchMonth();
        },

        nextMonth() {
            if (this.calMonth === 12) { this.calMonth = 1; this.calYear++; }
            else { this.calMonth++; }
            this.fetchMonth();
        },

        get calDays() {
            const firstDay    = new Date(this.calYear, this.calMonth - 1, 1).getDay();
            const daysInMonth = new Date(this.calYear, this.calMonth, 0).getDate();
            const cells       = Array(firstDay).fill(null);
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = `${this.calYear}-${String(this.calMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                cells.push({ day: d, date: dateStr, state: this.computeDayState(dateStr) });
            }
            return cells;
        },

        computeDayState(dateStr) {
            const slots = this.bookingsByDate[dateStr];
            if (!slots || slots.length === 0) return 'available';
            return this.isCoveredFully(slots) ? 'full' : 'partial';
        },

        isCoveredFully(slots) {
            const workStart = 7 * 60;  // 07:00
            const workEnd   = 15 * 60; // 15:00
            // Mescla ranges e verifica cobertura total
            const merged = this.mergeRanges(
                slots.filter(s => s.end).map(s => [this.toMinutes(s.start), this.toMinutes(s.end)])
            );
            return merged.length > 0 && merged[0][0] <= workStart && merged[0][1] >= workEnd;
        },

        mergeRanges(ranges) {
            if (!ranges.length) return [];
            const sorted = [...ranges].sort((a, b) => a[0] - b[0]);
            const result = [sorted[0]];
            for (let i = 1; i < sorted.length; i++) {
                const last = result[result.length - 1];
                if (sorted[i][0] <= last[1]) last[1] = Math.max(last[1], sorted[i][1]);
                else result.push(sorted[i]);
            }
            return result;
        },

        isPast(dateStr) {
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return new Date(dateStr + 'T00:00:00') < today;
        },

        isWeekend(dateStr) {
            const day = new Date(dateStr + 'T00:00:00').getDay();
            return day === 0 || day === 6;
        },

        dayClasses(cell) {
            if (!cell) return 'cursor-default';
            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
            const isToday      = cell.date === todayStr;
            const isPast       = this.isPast(cell.date);
            const isWeekend    = this.isWeekend(cell.date);
            const isSelected   = cell.date === this.selectedDate;
            const isExtSelected = this.externalSelectedDates.includes(cell.date);

            if (isPast || isWeekend) return 'text-gray-300 cursor-not-allowed';
            if (cell.state === 'full') return 'bg-red-100 text-red-400 cursor-not-allowed';
            if (isSelected) return 'bg-blue-600 text-white cursor-pointer font-bold ring-2 ring-blue-400';
            if (isExtSelected) return 'bg-blue-200 text-blue-900 cursor-pointer ring-1 ring-blue-400 font-bold';
            const base = isToday ? 'font-bold ring-2 ring-blue-300 ' : '';
            if (cell.state === 'partial') return base + 'bg-orange-50 hover:bg-orange-100 cursor-pointer';
            return base + 'bg-white hover:bg-blue-50 cursor-pointer';
        },

        selectDate(dateStr) {
            if (!dateStr || this.isPast(dateStr) || this.isWeekend(dateStr)) return;
            this.selectedDate  = dateStr;
            this.selectedStart = '';
            this.selectedEnd   = '';
            this.selectingEnd  = false;
            this.buildTimeSlots(dateStr);
            this.$dispatch('date-selected', { date: dateStr });
        },

        buildTimeSlots(dateStr) {
            const slots = [];
            for (let h = 7; h < 15; h++) {
                for (let m = 0; m < 60; m += 30) {
                    const time = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
                    slots.push({ time, occupied: this.isOccupied(dateStr, time) });
                }
            }
            this.timeSlots = slots;
        },

        isOccupied(dateStr, slotTime) {
            const ranges = this.bookingsByDate[dateStr] || [];
            const slotMin = this.toMinutes(slotTime);
            return ranges.some(r => {
                if (!r.end) return false;
                return slotMin >= this.toMinutes(r.start) && slotMin < this.toMinutes(r.end);
            });
        },

        toMinutes(timeStr) {
            if (!timeStr) return 0;
            const [h, m] = timeStr.split(':').map(Number);
            return h * 60 + m;
        },

        addMinutes(timeStr, mins) {
            const total = this.toMinutes(timeStr) + mins;
            return `${String(Math.floor(total / 60)).padStart(2,'0')}:${String(total % 60).padStart(2,'0')}`;
        },

        clickSlot(slot) {
            if (slot.occupied) return;
            if (!this.selectingEnd) {
                this.selectedStart = slot.time;
                this.selectedEnd   = '';
                this.selectingEnd  = true;
                return;
            }
            if (this.toMinutes(slot.time) <= this.toMinutes(this.selectedStart)) {
                // Reinicia seleção
                this.selectedStart = slot.time;
                this.selectedEnd   = '';
                return;
            }
            // Verifica se há slot ocupado entre início e fim
            const startMin = this.toMinutes(this.selectedStart);
            const endMin   = this.toMinutes(slot.time) + 30;
            const hasConflict = this.timeSlots.some(s =>
                s.occupied &&
                this.toMinutes(s.time) >= startMin &&
                this.toMinutes(s.time) < endMin
            );
            if (hasConflict) return;

            this.selectedEnd  = this.addMinutes(slot.time, 30);
            this.selectingEnd = false;
            this.$dispatch('time-selected', { start: this.selectedStart, end: this.selectedEnd });
        },

        slotClasses(slot) {
            if (slot.occupied) return 'bg-red-100 text-red-500 border-red-200 cursor-not-allowed opacity-70';

            const slotMin  = this.toMinutes(slot.time);
            const startMin = this.toMinutes(this.selectedStart);
            const endMin   = this.toMinutes(this.selectedEnd);

            if (this.selectedStart && this.selectedEnd) {
                if (slotMin >= startMin && slotMin < endMin) {
                    return 'bg-blue-500 text-white border-blue-600';
                }
            } else if (this.selectedStart && slotMin === startMin) {
                return 'bg-blue-500 text-white border-blue-600';
            }

            return 'bg-green-50 text-green-800 border-green-200 hover:bg-green-100 cursor-pointer';
        },
    }));
});
</script>
