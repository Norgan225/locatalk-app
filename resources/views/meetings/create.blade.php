<x-app-layout>
<div class="p-4 sm:p-6 lg:p-8 max-w-4xl mx-auto">
	<!-- Header -->
	<div class="mb-8">
		<a href="{{ route('web.meetings') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors mb-4">
			<svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
			</svg>
			Retour aux réunions
		</a>
		<h1 class="text-2xl md:text-3xl font-bold text-gray-900">📹 {{ org_trans('create_meeting') }}</h1>
		<p class="mt-1 text-sm text-gray-600">{{ org_trans('fill_info_create_project') }}</p>
	</div>

	<!-- Form -->
	<form action="{{ route('web.meetings.store') }}" method="POST" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
		@csrf

		<!-- Title -->
		<div class="mb-6">
			<label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('meeting_title') }} <span class="text-red-600">*</span>
			</label>
			<input
				type="text"
				id="title"
				name="title"
				value="{{ old('title') }}"
				required
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('title') border-red-500 @enderror"
				placeholder="{{ org_trans('meeting_title_placeholder') }}"
			>
			@error('title')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Description -->
		<div class="mb-6">
			<label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('description') }}
			</label>
			<textarea
				id="description"
				name="description"
				rows="4"
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('description') border-red-500 @enderror"
				placeholder="{{ org_trans('meeting_description_placeholder') }}"
			>{{ old('description') }}</textarea>
			@error('description')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Date & Time -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
			<!-- Start Date/Time -->
			<div>
				<label for="start_time" class="block text-sm font-semibold text-gray-900 mb-2">
					{{ org_trans('start_date_time') }} <span class="text-red-600">*</span>
				</label>
				<input
					type="datetime-local"
					id="start_time"
					name="start_time"
					value="{{ old('start_time') }}"
					required
					min="{{ now()->format('Y-m-d\TH:i') }}"
					class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('start_time') border-red-500 @enderror"
				>
				@error('start_time')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>

			<!-- Duration -->
			<div>
				<label for="duration" class="block text-sm font-semibold text-gray-900 mb-2">
					{{ org_trans('duration_minutes') }} <span class="text-red-600">*</span>
				</label>
				<select
					id="duration"
					name="duration"
					required
					class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('duration') border-red-500 @enderror"
				>
					<option value="15" {{ old('duration') == 15 ? 'selected' : '' }}>15 minutes</option>
					<option value="30" {{ old('duration', 30) == 30 ? 'selected' : '' }}>30 minutes</option>
					<option value="45" {{ old('duration') == 45 ? 'selected' : '' }}>45 minutes</option>
					<option value="60" {{ old('duration') == 60 ? 'selected' : '' }}>1 heure</option>
					<option value="90" {{ old('duration') == 90 ? 'selected' : '' }}>1h30</option>
					<option value="120" {{ old('duration') == 120 ? 'selected' : '' }}>2 heures</option>
					<option value="180" {{ old('duration') == 180 ? 'selected' : '' }}>3 heures</option>
				</select>
				@error('duration')
				<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<!-- Participants -->
		<div class="mb-6">
			<label for="participants" class="block text-sm font-semibold text-gray-900 mb-2">
				{{ org_trans('participants') }}
			</label>
			<select
				id="participants"
				name="participants[]"
				multiple
				class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('participants') border-red-500 @enderror"
				style="min-height: 200px;"
			>
				@foreach($users as $user)
				<option value="{{ $user->id }}" {{ collect(old('participants'))->contains($user->id) ? 'selected' : '' }}>
					{{ $user->name }} ({{ $user->email }})
				</option>
				@endforeach
			</select>
			<p class="mt-1 text-xs text-gray-500">{{ org_trans('select_participants_hint') }}</p>
			@error('participants')
			<p class="mt-1 text-sm text-red-600">{{ $message }}</p>
			@enderror
		</div>

		<!-- Recording Option -->
		<div class="mb-6">
			<div class="flex items-start">
				<div class="flex items-center h-5">
					<input
						type="checkbox"
						id="is_recorded"
						name="is_recorded"
						value="1"
						{{ old('is_recorded') ? 'checked' : '' }}
						class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
					>
				</div>
				<div class="ml-3">
					<label for="is_recorded" class="text-sm font-semibold text-gray-900">
						{{ org_trans('record_meeting') }}
					</label>
					<p class="text-xs text-gray-500">{{ org_trans('recording_available_after') }}</p>
				</div>
			</div>
		</div>

		<!-- Info: Meeting Link -->
		<div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
			<div class="flex items-start gap-3">
				<svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<div>
					<p class="text-sm font-semibold text-blue-900">{{ org_trans('meeting_link') }}</p>
					<p class="text-xs text-blue-700 mt-1">{{ org_trans('meeting_link_generated') }}</p>
				</div>
			</div>
		</div>

		<!-- Submit Buttons -->
		<div class="flex gap-4 pt-4 border-t border-gray-200">
			<button
				type="submit"
				class="flex-1 px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg transition-colors flex items-center justify-center"
			>
				<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
				</svg>
				{{ org_trans('create_meeting') }}
			</button>
			<a
				href="{{ route('web.meetings') }}"
				class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors text-center"
			>
				{{ org_trans('cancel') }}
			</a>
		</div>
	</form>
</div>

<script>
// Auto-calculate end time based on duration
document.getElementById('start_time').addEventListener('change', updateEndTime);
document.getElementById('duration').addEventListener('change', updateEndTime);

function updateEndTime() {
	const startTime = document.getElementById('start_time').value;
	const duration = parseInt(document.getElementById('duration').value);

	if (startTime && duration) {
		const start = new Date(startTime);
		const end = new Date(start.getTime() + duration * 60000);
		console.log('Fin prévue:', end.toLocaleString('fr-FR'));
	}
}
</script>
</x-app-layout>
