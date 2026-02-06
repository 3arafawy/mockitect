<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  mocks: Object,
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Mocks</h1>
        <Link
          href="/__mockitect/mocks/create"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Create Mock
        </Link>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <div v-if="mocks.data.length === 0" class="text-gray-500 text-center py-8">
            No mocks created yet. 
            <Link href="/__mockitect/mocks/create" class="text-indigo-600 hover:text-indigo-900">
              Create your first mock
            </Link>
          </div>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rules</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="mock in mocks.data" :key="mock.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ mock.name }}</div>
                    <div v-if="mock.description" class="text-sm text-gray-500 truncate max-w-xs">
                      {{ mock.description }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ mock.priority }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      :class="{
                        'px-2 py-1 text-xs font-semibold rounded-full': true,
                        'bg-green-100 text-green-800': mock.is_active,
                        'bg-gray-100 text-gray-800': !mock.is_active,
                      }"
                    >
                      {{ mock.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ mock.match_rules.length }} rules
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ mock.request_logs_count }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <Link
                      :href="`/__mockitect/mocks/${mock.id}/edit`"
                      class="text-indigo-600 hover:text-indigo-900 mr-4"
                    >
                      Edit
                    </Link>
                    <Link
                      :href="`/__mockitect/mocks/${mock.id}`"
                      method="delete"
                      as="button"
                      class="text-red-600 hover:text-red-900"
                      preserve-scroll
                    >
                      Delete
                    </Link>
                    <Link
                      :href="`/\_\_mockitect/mocks/${mock.id}`"
                      method="delete"
                      as="button"
                      class="text-red-600 hover:text-red-900"
                      preserve-scroll
                    >
                      Delete
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="mocks.links.length > 3" class="mt-4 flex justify-center">
            <div class="flex space-x-2">
              <Link
                v-for="(link, index) in mocks.links"
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
