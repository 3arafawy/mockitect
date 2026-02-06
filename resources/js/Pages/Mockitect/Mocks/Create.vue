<script setup>
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
  mock: {
    type: Object,
    default: () => ({
      name: '',
      description: '',
      priority: 0,
      is_active: true,
      match_rules: [
        { type: 'path', matcher: 'exact', value: '' },
        { type: 'method', matcher: 'exact', value: 'GET' },
      ],
      response_config: {
        type: 'static',
        status: 200,
        headers: { 'Content-Type': 'application/json' },
        body: '',
      },
    }),
  },
  isEditing: {
    type: Boolean,
    default: false,
  },
});

const form = useForm({
  name: props.mock.name,
  description: props.mock.description,
  priority: props.mock.priority,
  is_active: props.mock.is_active,
  match_rules: props.mock.match_rules,
  response_config: props.mock.response_config,
});

const submit = () => {
  if (props.isEditing) {
    form.put(`/__mockitect/mocks/${props.mock.id}`);
  } else {
    form.post('/__mockitect/mocks');
  }
};

const addHeaderRule = () => {
  form.match_rules.push({
    type: 'header',
    matcher: 'exists',
    name: '',
    value: '',
  });
};

const removeRule = (index) => {
  form.match_rules.splice(index, 1);
};
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">
          {{ isEditing ? 'Edit Mock' : 'Create Mock' }}
        </h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="bg-white shadow rounded-lg">
        <form @submit.prevent="submit" class="space-y-8 divide-y divide-gray-200 p-6">
          <!-- Basic Info -->
          <div class="space-y-6">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
              <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
              />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                <input
                  type="number"
                  id="priority"
                  v-model="form.priority"
                  min="0"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>

              <div class="flex items-center h-full pt-6">
                <input
                  type="checkbox"
                  id="is_active"
                  v-model="form.is_active"
                  class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                />
                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
              </div>
            </div>
          </div>

          <!-- Match Rules -->
          <div class="pt-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Match Rules</h3>
              <button
                type="button"
                @click="addHeaderRule"
                class="text-sm text-indigo-600 hover:text-indigo-900"
              >
                + Add Header Rule
              </button>
            </div>

            <div v-for="(rule, index) in form.match_rules" :key="index" class="mb-4 p-4 bg-gray-50 rounded-md">
              <div class="flex justify-between items-start mb-2">
                <span class="text-sm font-medium text-gray-700 capitalize">{{ rule.type }} Rule</span>
                <button
                  v-if="rule.type === 'header'"
                  type="button"
                  @click="removeRule(index)"
                  class="text-red-600 hover:text-red-900 text-sm"
                >
                  Remove
                </button>
              </div>

              <div class="grid grid-cols-3 gap-4">
                <div v-if="rule.type === 'header'">
                  <label class="block text-xs text-gray-500">Header Name</label>
                  <input
                    v-model="rule.name"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  />
                </div>
                <div>
                  <label class="block text-xs text-gray-500">Matcher</label>
                  <select
                    v-model="rule.matcher"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  >
                    <option value="exact">Exact</option>
                    <option value="prefix">Prefix</option>
                    <option value="regex">Regex</option>
                    <option value="wildcard">Wildcard</option>
                    <option v-if="rule.type === 'header'" value="exists">Exists</option>
                    <option v-if="rule.type === 'header'" value="contains">Contains</option>
                    <option v-if="rule.type === 'method'" value="any">Any</option>
                  </select>
                </div>
                <div v-if="rule.matcher !== 'exists'">
                  <label class="block text-xs text-gray-500">Value</label>
                  <input
                    v-model="rule.value"
                    type="text"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- Response Config -->
          <div class="pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Response Configuration</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Status Code</label>
                <input
                  v-model="form.response_config.status"
                  type="number"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Content Type</label>
                <select
                  v-model="form.response_config.headers['Content-Type']"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                  <option value="application/json">application/json</option>
                  <option value="text/plain">text/plain</option>
                  <option value="text/html">text/html</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Response Body</label>
              <textarea
                v-model="form.response_config.body"
                rows="6"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm"
              />
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-6 flex justify-end space-x-4">
            <Link
              href="/__mockitect/mocks"
              class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ isEditing ? 'Update Mock' : 'Create Mock' }}
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</template>
