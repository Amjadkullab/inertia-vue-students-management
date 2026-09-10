<script setup>
import MagnifyingGlass from "@/Components/Icons/MagnifyingGlass.vue";
import Pagination from "@/Components/Pagination.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";

import {
    Head,
    Link,
    router,
    useForm,
    usePage,
} from "@inertiajs/vue3";

import { ref, watch, computed } from "vue";

const props = defineProps({
    students: {
        type: Object,
        required: true,
    },

    classes: {
        type: Array,
        default: () => [],
    },
    class_id: {
        type: [String, Number],
        default: "", },
});

/*
|--------------------------------------------------------------------------
| Search & Filter
|--------------------------------------------------------------------------
*/

const search = ref(usePage().props.search ?? "");
const class_id = ref(usePage().props.class_id ?? "");
const pageNumber = ref(1);

/*
|--------------------------------------------------------------------------
| Students URL
|--------------------------------------------------------------------------
*/

const studentsUrl = computed(() => {
    const params = new URLSearchParams();

    params.set("page", pageNumber.value);

    if (search.value) {
        params.set("search", search.value);
    }

    if (class_id.value) {
        params.set("class_id", class_id.value);
    }

    return `/students?${params.toString()}`;
});

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pageNumberUpdated = (link) => {
    if (!link?.url) {
        return;
    }

    const url = new URL(link.url, window.location.origin);

    pageNumber.value = url.searchParams.get("page") ?? 1;
};

/*
|--------------------------------------------------------------------------
| Search / Filter Watch
|--------------------------------------------------------------------------
*/

watch(
    () => studentsUrl.value,
    (updatedStudentsUrl) => {
        router.visit(updatedStudentsUrl, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        });
    }
);

/*
|--------------------------------------------------------------------------
| Delete Student
|--------------------------------------------------------------------------
*/

const deleteForm = useForm();

const deleteStudent = (studentId) => {
    if (confirm("Are you sure you want to delete this student?")) {
        deleteForm.delete(`/students/${studentId}`, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Students" />

    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Students
            </h2>
        </template>

        <!-- Page -->
        <div class="w-full bg-gray-100 py-10">
            <div class="w-full px-4 sm:px-6 lg:px-8">

                <!-- Page Header -->
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <!-- Title -->
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900">
                            Students
                        </h1>

                        <p class="mt-2 text-sm text-gray-700">
                            A list of all the Students.
                        </p>
                    </div>

                    <!-- Add Student -->
                    <div>
                        <Link href="/students/create"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Add Student
                        </Link>
                    </div>
                </div>

                <!-- Search + Filter -->
                <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center">
                    <!-- Search -->
                    <div class="relative w-full text-sm text-gray-800 sm:w-80">
                        <!-- Search Icon -->
                        <div
                            class="pointer-events-none absolute left-0 top-0 bottom-0 flex items-center pl-3 text-gray-500">
                            <MagnifyingGlass />
                        </div>

                        <input id="search" v-model="search" type="text" placeholder="Search students data..."
                            class="block w-full rounded-lg border-0 py-2 pl-10 pr-3 text-gray-900 ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                    </div>

                    <!-- Class Filter -->
                    <select v-model="class_id"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:w-64 sm:text-sm sm:leading-6">
                        <option value="">
                            Filter By Class
                        </option>

                        <option v-for="item in classes" :key="item.id" :value="item.id">
                            {{ item.name }}
                        </option>
                    </select>
                </div>

                <!-- Table -->
                <div class="mt-8 w-full">
                    <div class="w-full">

                        <!-- Table Container -->
                        <div class="w-full overflow-hidden rounded-lg bg-white shadow ring-1 ring-black ring-opacity-5">
                            <table class="w-full table-auto divide-y divide-gray-300">
                                <!-- Table Header -->
                                <thead class="bg-gray-50">
                                    <tr>
                                        <!-- ID -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            ID
                                        </th>

                                        <!-- Name -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Name
                                        </th>

                                        <!-- Email -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Email
                                        </th>

                                        <!-- Class -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Class
                                        </th>

                                        <!-- Section -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Section
                                        </th>

                                        <!-- Created At -->
                                        <th scope="col" class="px-4 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Created At
                                        </th>

                                        <!-- Actions -->
                                        <th scope="col" class="px-4 py-3.5 text-right text-sm font-semibold text-gray-900">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>

                                <!-- Table Body -->
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <tr v-for="student in students.data" :key="student.id"
                                        class="transition hover:bg-gray-50">
                                        <!-- ID -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                                            {{ student.id }}
                                        </td>

                                        <!-- Name -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                                            {{ student.name }}
                                        </td>

                                        <!-- Email -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                            {{ student.email }}
                                        </td>

                                        <!-- Class -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                            {{ student.class?.name ?? "-" }}
                                        </td>

                                        <!-- Section -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                            {{ student.section?.name ?? "-" }}
                                        </td>

                                        <!-- Created At -->
                                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-500">
                                            {{
                                                student.created_at_formatted ??
                                                student.created_at ??
                                                "-"
                                            }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-medium">
                                            <Link :href="`/students/${student.id}/edit`"
                                                class="text-indigo-600 transition hover:text-indigo-900">
                                            Edit
                                            </Link>

                                            <button type="button" @click="
                                                deleteStudent(student.id)
                                                " class="ml-3 text-red-600 transition hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- No Students -->
                                    <tr v-if="students.data.length === 0">
                                        <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                                            No students found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-5 w-full">
                            <Pagination :data="students" :pageNumberUpdated="pageNumberUpdated" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
