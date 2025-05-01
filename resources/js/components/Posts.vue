<script setup>
import { reactive, ref, onMounted } from 'vue';
import axios from 'axios';

const postFormData = reactive({
    title: '',
    content: '',
    editMode: false,
    editId: null,
    image: null
});

const imageInput = ref(null);

// posts must be an object with `data` and `links`
const posts = ref({
    data: [],
    links: [],
});

// Fetch existing posts from database through API
const fetchPosts = async (url = '/api/posts') => {
    try {
        const response = await axios.get(url);
        posts.value = response.data; // full response, not just data.data
    } catch (error) {
        console.log('Error fetching posts', error);
    }
}

onMounted(() => {
    fetchPosts();
});

const editPost = (post) => {
    postFormData.editMode = true;
    postFormData.editId = post.id;
    postFormData.title = post.title;
    postFormData.content = post.content;
}

const savePost = async () => {

    try {

        const formData = new FormData();
        formData.append('title', postFormData.title);
        formData.append('content', postFormData.content);

        if (postFormData.image) {
            formData.append('image', postFormData.image);
        }

        if (postFormData.editMode) {

            formData.append('_method', 'PUT');
            await axios.post(`/api/update-post/${postFormData.editId}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            postFormData.editMode = false;
            alert('Post updated successfully');
        } else {
            await axios.post('/api/add-post', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            alert('Post created successfully');
        }

        fetchPosts();

        // Clear form after successful submission
        postFormData.title = '';
        postFormData.content = '';
        postFormData.image = null;
        imageInput.value.value = null;

    } catch (error) {
        console.log('Error saving post', error);
    }
}

const deletePost = async (id) => {
    try {
        await axios.delete(`/api/delete-post/${id}`);
        fetchPosts();
        alert('Post deleted successfully');
    } catch (error) {
        console.log('Error deleting post', error);
    }
}


const handleFileUpload = (event) => {

    postFormData.image = event.target.files[0];

}
</script>


<template>
    <div class="max-w-4xl mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6 text-center">Posts</h1>

        <!-- Form -->
        <form class="mb-6 bg-white shadow-md rounded-md p-4" @submit.prevent="savePost">
            <div class="mb-4">
                <label for="title" class="block text-gray-700 font-bold mb-2">Title</label>
                <input type="text" v-model="postFormData.title" id="title"
                    class="w-full border rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Enter title">
            </div>
            <div class="mb-4">
                <label for="content" class="block text-gray-700 font-bold mb-2">Content</label>
                <textarea id="content" v-model="postFormData.content"
                    class="w-full border rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Enter content"></textarea>
            </div>

            <div class="mb-4">
                <input type="file"
                    class="w-full border rounded-md p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    @change="handleFileUpload" ref="imageInput">
            </div>

            <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded">
                {{ postFormData.editMode ? 'Update' : 'Create' }}
            </button>
        </form>

        <!-- Posts List -->
        <div class="mt-5 mb-6 bg-white shadow-md rounded-md p-4" v-for="post in posts.data" :key="post.id">
            <h3 class="text-lg font-semibold mb-2">{{ post.title }}</h3>
            <p class="text-gray-600">{{ post.content }}</p>

            <img v-if="post.image" :src="`/storage/images/${post.image}`" :alt="post.image"
                class="mt-3 mb-3 w-1/2 mx-auto rounded md:w-1/4 md:mx-0">

            <button type="button" @click="editPost(post)"
                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded mt-3 mr-3">Edit</button>
            <button type="button" @click="deletePost(post.id)"
                class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded mt-3">Delete</button>
        </div>

        <!-- Pagination -->
        <div class="flex flex-wrap justify-center" v-if="posts.links && posts.links.length">
            <button v-for="(link, index) in posts.links" :key="index" @click="link.url && fetchPosts(link.url)"
                v-html="link.label" :disabled="!link.url"
                class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded mt-3 mr-3 disabled:opacity-50"></button>
        </div>
    </div>
</template>
