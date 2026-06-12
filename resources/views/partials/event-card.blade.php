@php
  $colors = ['', '--green', '--purple'];
  $colorIndex = crc32($event->id) % 3;
  $colorClass = $colorIndex < 0 ? $colors[abs($colorIndex)] : $colors[$colorIndex];
  $initials = strtoupper(substr($event->speaker->name ?? '?', 0, 1))
            . strtoupper(substr(strstr($event->speaker->name ?? ' ', ' ') ?: ' ', 1, 1));
@endphp
<div class="event-card" data-event-id="{{ $event->id }}">
  <div class="event-card__img event-card__img{{ $colorClass }}">
    @if($event->image_url)
      <img src="{{ $event->image_url }}" alt="{{ $event->title }}">
    @endif
    <div class="event-card__img-title">{{ $event->title }}</div>
    <div class="event-date-badge">
      <div class="month">{{ $event->date->translatedFormat('M') }}</div>
      <div class="day">{{ $event->date->format('d') }}</div>
    </div>
    @if($event->status === 'completed')
      <div class="status-badge status-badge--completed"><i class="fas fa-check-circle"></i> Realizado</div>
    @endif
  </div>
  <div class="event-card__body">
    <h3>{{ $event->title }}</h3>
    @if($event->speaker)
      <div class="event-card__speaker">
        @if($event->speaker->photoUrl())
          <img src="{{ $event->speaker->photoUrl() }}" class="mini-avatar" style="object-fit:cover;padding:0">
        @else
          <div class="mini-avatar">{{ $initials }}</div>
        @endif
        <div>
          <div class="event-card__speaker-name">{{ $event->speaker->name }}</div>
          @if($event->speaker->bio)
            <div class="event-card__speaker-bio">{{ Str::limit($event->speaker->bio, 60) }}</div>
          @endif
        </div>
      </div>
    @endif
    <div class="event-card__metas">
      <div class="event-card__meta">
        <i class="fas fa-clock"></i>
        {{ substr($event->start_time, 0, 5) }}h às {{ $event->endTime() }}h
      </div>
      <div class="event-card__meta">
        <i class="fas fa-users"></i>
        {{ $event->max_capacity }} participantes
      </div>
    </div>
    <div class="event-card__footer">
      @if($event->status === 'completed')
        <span class="spots-info">{{ $event->participants->count() }} inscritos</span>
        <span class="btn-inscricao btn-inscricao--disabled">Encerrado</span>
      @elseif($event->isFull())
        <span class="spots-info spots-full">Esgotado · {{ $event->max_capacity }} inscritos</span>
        <a href="{{ route('login') }}" class="btn-inscricao" style="background:var(--muted)">Lista de espera</a>
      @else
        <span class="spots-info"><span>{{ $event->availableSpots() }}</span> de {{ $event->max_capacity }} vagas</span>
        @if($showActions ?? true)
          <a href="{{ route('login') }}" class="btn-inscricao">Inscrever-se <i class="fas fa-arrow-right"></i></a>
        @endif
      @endif
    </div>
  </div>
</div>
