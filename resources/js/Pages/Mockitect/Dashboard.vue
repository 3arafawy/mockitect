<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  stats: Object,
  recentMocks: Array,
  recentLogs: Array,
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Mockitect Dashboard</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Stats -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="text-sm font-medium text-gray-500 truncate">Total Mocks</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalMocks }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="text-sm font-medium text-gray-500 truncate">Active Mocks</div>
                <div class="mt-1 text-3xl font-semibold text-green-600">{{ stats.activeMocks }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="text-sm font-medium text-gray-500 truncate">Total Requests</div>
                <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalRequests }}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="text-sm font-medium text-gray-500 truncate">Matched Requests</div>
                <div class="mt-1 text-3xl font-semibold text-blue-600">{{ stats.matchedRequests }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Mocks -->
      <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Mocks</h3>
          <div class="mt-4">
            <div v-if="recentMocks.length === 0" class="text-gray-500">
              No mocks created yet.
            </div>
            <ul v-else class="divide-y divide-gray-200">
              <li v-for="mock in recentMocks" :key="mock.id" class="py-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ mock.name }}</p>
                    <p class="text-sm text-gray-500">
                      Priority: {{ mock.priority }} | 
                      Status: {{ mock.is_active ? 'Active' : 'Inactive' }} |
                      Requests: {{ mock.request_logs_count }}
                    </p>
                  </div>
                  <Link
                    :href="`/__mockitect/mocks/${mock.id}`"
                    class="text-indigo-600 hover:text-indigo-900 text-sm"
                  >
                    View
                  </Link>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Recent Logs -->
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Request Logs</h3>
          <div class="mt-4">
            <div v-if="recentLogs.length === 0" class="text-gray-500">
              No requests logged yet.
            </div>
            <ul v-else class="divide-y divide-gray-200">
              <li v-for="log in recentLogs" :key="log.id" class="py-4">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      <span :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full mr-2': true,
                        'bg-green-100 text-green-800': log.was_matched,
                        'bg-red-100 text-red-800': !log.was_matched
                      }">
                        {{ log.method }}
                      </span>
                      {{ log.path }}
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                      Status: {{ log.response_status }} | 
                      {{ log.created_at }}
                    </p>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
