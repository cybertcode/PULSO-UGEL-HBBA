@props(['on'])

<span class="text-success small fw-medium"
  x-data="{ shown: false, timeout: null }"
  x-init="@this.on('{{ $on }}', () => {
    clearTimeout(timeout);
    shown = true;
    timeout = setTimeout(() => { shown = false }, 2000);
  })"
  x-show="shown"
  x-transition:leave="transition-opacity duration-1000"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  style="display: none;"
  {{ $attributes }}>
  <i class="ti tabler-check me-1"></i>{{ $slot->isEmpty() ? 'Guardado.' : $slot }}
</span>
