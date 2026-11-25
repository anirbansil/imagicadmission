{{--
  Centre address and check-in location, shared by Centre Master (add / edit, admin)
  and My Centre (centre head). Expects $center (CenterMaster or null).
--}}
@php
  $loc = function ($field) use ($center) { return old($field, optional($center)->$field); };
  $hasLocation = is_numeric(optional($center)->latitude) && is_numeric(optional($center)->longitude);
@endphp
<style>
  .ctr-loc { border: 1px solid #e6dcdc; border-radius: 8px; padding: 16px 18px; margin: 18px 0; background: #fff; }
  .ctr-loc h3 { font-size: 17px; margin: 0 0 4px; color: #2B2022; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .ctr-loc .ctr-pill { font-size: 12px; font-weight: 600; border-radius: 20px; padding: 1px 10px; }
  .ctr-loc .ctr-pill.on { background: #dff3e4; color: #1d6b35; }
  .ctr-loc .ctr-pill.off { background: #eef1f5; color: #46566d; }
  .ctr-loc .ctr-help { font-size: 13px; color: #6f6466; margin: 0 0 14px; }
  .ctr-loc .ctr-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px 16px; }
  .ctr-loc .ctr-grid .wide { grid-column: 1 / -1; }
  .ctr-loc label { display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px; }
  .ctr-loc textarea { min-height: 64px; resize: vertical; }
  .ctr-loc small.ctr-hint { display: block; color: #8a7f81; font-size: 12px; margin-top: 3px; }
  .ctr-loc .ctr-tools { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-top: 12px; }
  .ctr-loc .ctr-tools button, .ctr-loc .ctr-tools a { font-size: 13px; padding: 6px 12px; border-radius: 6px; border: 1px solid #571d1f; color: #571d1f; background: #fff; text-decoration: none; cursor: pointer; }
  .ctr-loc .ctr-tools button:disabled { opacity: .6; cursor: wait; }
  .ctr-loc .ctr-status { font-size: 13px; color: #6f6466; }
  .ctr-loc .ctr-status.err { color: #b3261e; }
  .ctr-loc .error { color: #b3261e; font-size: 12px; }
  @media (max-width: 767px) { .ctr-loc .ctr-grid { grid-template-columns: 1fr; } }
</style>
<div class="ctr-loc" id="ctr-loc">
  <h3>
    Address, email &amp; check-in location
    <span class="ctr-pill {{$hasLocation ? 'on' : 'off'}}">{{$hasLocation ? 'Location check on' : 'Location check off'}}</span>
  </h3>
  <p class="ctr-help">Students of this centre can check in only when they are inside the centre (within the allowed distance of this point). Check-out works from anywhere. Leave latitude and longitude empty to turn the location check off for this centre.</p>
  <div class="ctr-grid">
    <div class="wide">
      <label for="ctr-address">Centre address</label>
      <textarea name="address" id="ctr-address" class="form-control" maxlength="500" placeholder="Building, street, area, city, PIN">{{$loc('address')}}</textarea>
      @if($errors->has('address'))<span class="error">{{$errors->first('address')}}</span>@endif
    </div>
    <div class="wide">
      <label for="ctr-email">Centre email (for notifications)</label>
      <input type="email" name="email" id="ctr-email" class="form-control" maxlength="191" value="{{$loc('email')}}" placeholder="e.g. chandnichowk@imagic.net.in">
      <small class="ctr-hint">New registrations, students' leave / fix / online class requests and attendance alerts are emailed here, with admin in CC. If empty, they go to this centre's login account email.</small>
      @if($errors->has('email'))<span class="error">{{$errors->first('email')}}</span>@endif
    </div>
    <div>
      <label for="ctr-lat">Latitude</label>
      <input type="text" inputmode="decimal" name="latitude" id="ctr-lat" class="form-control" value="{{$loc('latitude') !== null ? rtrim(rtrim((string) $loc('latitude'), '0'), '.') : ''}}" placeholder="e.g. 22.5726459">
      @if($errors->has('latitude'))<span class="error">{{$errors->first('latitude')}}</span>@endif
    </div>
    <div>
      <label for="ctr-lng">Longitude</label>
      <input type="text" inputmode="decimal" name="longitude" id="ctr-lng" class="form-control" value="{{$loc('longitude') !== null ? rtrim(rtrim((string) $loc('longitude'), '0'), '.') : ''}}" placeholder="e.g. 88.3638953">
      @if($errors->has('longitude'))<span class="error">{{$errors->first('longitude')}}</span>@endif
    </div>
    <div>
      <label for="ctr-radius">Allowed distance (metres)</label>
      <input type="number" name="geo_radius" id="ctr-radius" class="form-control" min="50" max="2000" step="10" value="{{$loc('geo_radius') ?: config('attendance.defaults.geo_radius')}}">
      <small class="ctr-hint">How far from this point a student may be. 150–300 m suits most buildings.</small>
      @if($errors->has('geo_radius'))<span class="error">{{$errors->first('geo_radius')}}</span>@endif
    </div>
  </div>
  <div class="ctr-tools">
    <button type="button" id="ctr-use-here"><i class="fa fa-crosshairs" aria-hidden="true"></i> Use my current location</button>
    <a href="#" id="ctr-find" target="_blank" rel="noopener"><i class="fa fa-search" aria-hidden="true"></i> Find address on Google Maps</a>
    <a href="#" id="ctr-view" target="_blank" rel="noopener" @if(!$hasLocation) hidden @endif><i class="fa fa-map-marker" aria-hidden="true"></i> Check the point on the map</a>
    <span class="ctr-status" id="ctr-status" aria-live="polite"></span>
  </div>
  <small class="ctr-hint" style="margin-top:10px">Tip: stand inside the centre and tap <strong>Use my current location</strong>, or in Google Maps right-click the building and click the numbers at the top of the menu to copy them, then paste here (you can paste both numbers into Latitude).</small>
  @if(optional($center)->location_updated_at)
    <small class="ctr-hint">Location last changed {{\Carbon\Carbon::parse($center->location_updated_at)->timezone(config('attendance.timezone'))->format('d-M-Y h:i A')}}@if($center->locationEditor) by {{$center->locationEditor->name}}@endif.</small>
  @endif
</div>
<script>
(function () {
  var lat = document.getElementById('ctr-lat'), lng = document.getElementById('ctr-lng'),
      address = document.getElementById('ctr-address'), status = document.getElementById('ctr-status'),
      view = document.getElementById('ctr-view'), find = document.getElementById('ctr-find'), here = document.getElementById('ctr-use-here');

  function say(text, error) { status.textContent = text || ''; status.className = 'ctr-status' + (error ? ' err' : ''); }
  function refresh() {
    var a = parseFloat(lat.value), b = parseFloat(lng.value);
    var ok = isFinite(a) && isFinite(b) && Math.abs(a) <= 90 && Math.abs(b) <= 180;
    view.hidden = !ok;
    if (ok) { view.href = 'https://www.google.com/maps?q=' + a + ',' + b; }
    find.href = 'https://www.google.com/maps/search/' + encodeURIComponent(address.value.trim() || 'IMAGIC');
  }
  // Pasting "22.57264, 88.36389" (as copied from Google Maps) into either box fills both.
  [lat, lng].forEach(function (input) {
    input.addEventListener('input', function () {
      var m = input.value.match(/^\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*$/);
      if (m) { lat.value = m[1]; lng.value = m[2]; }
      refresh();
    });
  });
  address.addEventListener('input', refresh);
  here.addEventListener('click', function () {
    if (!window.isSecureContext || !navigator.geolocation) {
      say('This browser cannot share your location here. Open the site with https:// or paste the numbers from Google Maps.', true);
      return;
    }
    here.disabled = true; say('Finding your location…');
    navigator.geolocation.getCurrentPosition(function (p) {
      lat.value = p.coords.latitude.toFixed(7); lng.value = p.coords.longitude.toFixed(7);
      here.disabled = false; refresh();
      say('Location filled in (accurate to about ' + Math.round(p.coords.accuracy) + ' m)' + (p.coords.accuracy > 100 ? '. That is not very precise; try again near a window or with GPS on.' : '. Click Save to keep it.'), p.coords.accuracy > 100);
    }, function (err) {
      here.disabled = false;
      say(err.code === 1 ? 'Location access is blocked. Allow location for this site and try again.' : 'Could not find your location. Turn on location / GPS and try again.', true);
    }, { enableHighAccuracy: true, timeout: 20000, maximumAge: 0 });
  });
  refresh();
})();
</script>
