{{--
@if (session('success_message'))
    <div
        x-data="{ isVisible: true }"
        x-init="
                    setTimeout(() => {
                        isVisible = false
                    }, 5000)
                "
        x-show.transition.duration.1000ms="isVisible"
        class="text-green mt-4"
    >
        {{ session('success_message') }}
    </div>
@endif

@if (session('danger_message'))
    <div
        x-data="{ isVisible: true }"
        x-init="
                    setTimeout(() => {
                        isVisible = false
                    }, 5000)
                "
        x-show.transition.duration.1000ms="isVisible"
        class="text-red mt-4"
    >
        {{ session('danger_message') }}
    </div>
@endif

@if (session('warning_message'))
    <div
        x-data="{ isVisible: true }"
        x-init="
                    setTimeout(() => {
                        isVisible = false
                    }, 5000)
                "
        x-show.transition.duration.1000ms="isVisible"
        class="text-yellow mt-4"
    >
        {{ session('warning_message') }}
    </div>
@endif
--}}
