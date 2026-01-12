<form action="{{ route('setLanguage') }}" method="POST" class="inline">
  @csrf
  <select name="lang" id="lang" onchange="this.form.submit()" class="form-select">
    <option value="pt" {{ app()->getLocale() == 'pt' ? 'selected' : '' }}>Português</option>
    <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
    <option value="es" {{ app()->getLocale() == 'es' ? 'selected' : '' }}>Español</option>
  </select>
</form>