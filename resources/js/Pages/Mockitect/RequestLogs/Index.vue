<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  logs: Object,
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Request Logs</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div v-if="logs.data.length === 0" class="text-gray-500 text-center py-8">
            No request logs found.
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Path</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Matched</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ new Date(log.created_at).toLocaleString() }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                        'bg-blue-100 text-blue-800': log.method === 'GET',
                        'bg-green-100 text-green-800': log.method === 'POST',
                        'bg-yellow-100 text-yellow-800': log.method === 'PUT',
                        'bg-red-100 text-red-800': log.method === 'DELETE',
                      }"
                    >
                      {{ log.method }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ log.path }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': log.response_status >= 200 && log.response_status < 300,
                        'bg-yellow-100 text-yellow-800': log.response_status >= 300 && log.response_status < 400,
                        'bg-red-100 text-red-800': log.response_status >= 400,
                      }"
                    >
                      {{ log.response_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': log.was_matched,
                        'bg-gray-100 text-gray-800': !log.was_matched,
                      }"
                    >
                      {{ log.was_matched ? 'Yes' : 'No' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ log.response_time_ms }}ms
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="logs.links.length > 3" class="mt-4 flex justify-center">
            <div class="flex space-x-2">
              <Link
                v-for="(link, index) in logs.links"
                :key="index"
                :href="link.url"
                :class="{
                  'px-3 py-1 rounded text-sm': true,
                  'bg-indigo-600 text-white': link.active,
                  'bg-gray-200 text-gray-700 hover:bg-gray-300': !link.active && link.url,
                  'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url,
                }"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
