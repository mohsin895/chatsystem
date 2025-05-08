<template>
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-sm">
      <!-- Ticket Header -->
      <div class="p-6 border-b">
        <div class="flex items-start justify-between">
          <div>
            <h1 class="text-2xl font-bold text-gray-900" v-if="dataInfo">{{ dataInfo.subject }}</h1>
            <div class="mt-1 flex items-center">
              <span v-if="dataInfo" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                    :class="statusClasses[dataInfo.status]">
                {{ dataInfo.status }}
              </span>
              <span class="mx-2 text-gray-500">•</span>
              <span class="text-sm text-gray-500" v-if="dataInfo">Ticket #{{ dataInfo.ticketId }}</span>
            </div>
          </div>
          <!-- <button class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-colors">
            Update Status
          </button> -->
        </div>
        
        <div class="mt-4 text-gray-700" v-if="dataInfo">
          {{ dataInfo.description }}
        </div>
        
        <div class="mt-6 flex items-center">
          <div class="flex-shrink-0" v-if="dataInfo">
            <img class="h-10 w-10 rounded-full"  :src="dataInfo.avatar  || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(dataInfo.user.name) " alt="" />
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900" v-if="dataInfo">{{ dataInfo.user.name }}</p>
            <p class="text-sm text-gray-500" v-if="dataInfo"> Created on {{ formatDate(new Date(dataInfo.created_at)) }}</p>
          </div>
        </div>
      </div>
      
      <!-- Comments Section -->
      <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900" v-if="dataInfo">Comments ({{ dataInfo.comments.length }})</h2>
        
        <!-- New Comment Form -->
        <div class="mt-4 flex">
          <div class="flex-shrink-0" v-if="dataInfo">
            <img class="h-10 w-10 rounded-full" :src="dataInfo.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(currentUser.name)" alt="" />
          </div>
          <div class="ml-3 flex-1">
            <div class="border border-gray-300 rounded-lg shadow-sm overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
              <textarea
                v-model="newComment"
                rows="3"
                class="block w-full py-3 px-4 border-0 resize-none focus:ring-0 sm:text-sm"
                placeholder="Add a comment..."
              ></textarea>
              <div class="py-2 px-3 border-t border-gray-200 flex justify-end">
                <button
                  @click="addComment"
                  :disabled="!newComment.trim()"
                  class="px-4 py-2 bg-primary text-white cursor-pointer rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Comment
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Comments List -->
        <div class="mt-6 space-y-6" v-if="dataInfo">
          <div v-for="comment in dataInfo.comments" :key="comment.id" class="flex">
            <div class="flex-shrink-0">
              <img class="h-10 w-10 rounded-full" :src="comment.user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(comment.user.name)" alt="" />
            </div>
            <div class="ml-3 flex-1">
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex justify-between">
                  <div>
                 
                    <p class="text-xs text-gray-500">{{ formatDate(comment.createdAt) }}</p>
                  </div>
                  <button class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                  </button>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                  <p>{{ comment.comment }}</p>
                </div>
                <div class="mt-2 flex space-x-4">
                  
                  <button 
                    @click="toggleReplyForm(comment)"
                    class="flex items-center text-sm text-gray-500 cursor-pointer hover:text-gray-700"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    Reply
                  </button>
                </div>
              </div>
              
              <!-- Reply Form -->
              <div v-if="comment.showReplyForm" class="mt-2" :id="`reply-form-${comment.id}`">
                <div class="flex">
                  <div class="flex-shrink-0">
                    <img class="h-8 w-8 rounded-full" :src="dataInfo.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(currentUser.name)" alt="" />
                  </div>
                  <div class="ml-2 flex-1">
                    <div class="border border-gray-300 rounded-lg shadow-sm overflow-hidden focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                      <div v-if="comment.replyingTo" class="bg-gray-50 px-3 py-1 text-xs text-gray-500 border-b">
                        Replying to <span class="font-medium">{{ comment.replyingTo.user.name }}</span>
                        <button 
                          @click="comment.replyingTo = null; comment.replyText = '';" 
                          class="ml-2 text-primary hover:text-primary/80"
                        >
                          Cancel reply
                        </button>
                      </div>
                      <textarea
                        v-model="comment.replyText"
                        rows="2"
                        class="block w-full py-2 px-3 border-0 resize-none focus:ring-0 sm:text-sm"
                        placeholder="Add a reply..."
                      ></textarea>
                      <div class="py-2 px-3 border-t border-gray-200 flex justify-end space-x-2">
                        <button
                          @click="toggleReplyForm(comment)"
                          class="px-3 py-1 text-gray-700 rounded-md hover:bg-gray-100 transition-colors"
                        >
                          Cancel
                        </button>
                        <button
                          @click="addReply(comment)"
                          :disabled="!comment.replyText?.trim()"
                          class="px-3 py-1 bg-primary cursor-pointer text-white rounded-md hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          Reply
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Replies -->
              <div v-if="comment.replies && comment.replies.length > 0" class="mt-2">
                <!-- Reply count and toggle -->
                <div v-if="comment.replies.length > 2" class="ml-6 mb-2">
                  <button 
                    @click="comment.showAllReplies = !comment.showAllReplies" 
                    class="text-sm text-primary flex items-center"
                  >
                    <svg 
                      xmlns="http://www.w3.org/2000/svg" 
                      class="h-4 w-4 mr-1" 
                      :class="{ 'rotate-90': comment.showAllReplies }"
                      fill="none" 
                      viewBox="0 0 24 24" 
                      stroke="currentColor"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    {{ comment.showAllReplies ? 'Hide' : 'View' }} all {{ comment.replies.length }} replies
                  </button>
                </div>
                
                <!-- Replies list -->
                <div class="space-y-4">
                  <template v-for="(reply, index) in comment.replies" :key="reply.id">
                    <div 
                      v-if="comment.showAllReplies || index < 2" 
                      class="flex ml-6 mt-2"
                    >
                      <div class="flex-shrink-0">
                        <img class="h-8 w-8 rounded-full" :src="reply.user.avatar  || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(reply.user.name)" alt="" />
                      </div>
                      <div class="ml-2 flex-1">
                        <div class="bg-gray-50 p-3 rounded-lg">
                          <div class="flex justify-between">
                            <div>
                              <h3 class="text-sm font-medium text-gray-900">{{ reply.user.name }}</h3>
                              <p class="text-xs text-gray-500">{{ formatDate(reply.createdAt) }}</p>
                            </div>
                          </div>
                          <div class="mt-1 text-sm text-gray-700">
                            <p>{{ reply.comment }}</p>
                          </div>
                          <div class="mt-1 flex space-x-3">
                           
                            <button 
                              @click="replyToReply(comment, reply)"
                              class="flex items-center text-xs text-gray-500 cursor-pointer hover:text-gray-700"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                              </svg>
                              Reply
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  import axios from "axios";
  import { ref, onBeforeMount,inject } from "vue";
  import { useToast } from "vue-toastification";
  import { useRoute, useRouter } from 'vue-router';
  const toast = useToast();
  const route = useRoute();
  const router = useRouter();
  const dataId = ref(route.params.dataId);
  const baseUrl = inject('$baseUrl');
  const dataInfo = ref(null);
  // Current user (would come from auth system in a real app)

  
  const fetchDataInfoList = async () => {
  
  try {
    const token = window.localStorage.getItem("token");

    if (!token) {
      console.error("Authentication token is missing.");
      return;
    }

    const config = {
      headers: {
        Authorization: "Bearer " + token,
      },
    };

    // Fetch designations for the selected department using dataId
    const response = await axios.get("/admin/ticket/info", {
      params: { dataId: dataId.value }, // Use params object to pass query parameters
      headers: config.headers
    });
    dataInfo.value = response.data.dataInfo;
  

   
   
   
  } catch (error) {
    console.error("Error fetching designation data:", error);
  }
};
  const currentUser = reactive({

  });
  

  
  // Status classes for styling
  const statusClasses = {
    'open': 'bg-green-100 text-green-800',
    'inprogress': 'bg-blue-100 text-blue-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'resolved': 'bg-gray-100 text-gray-800',
    'closed': 'bg-red-100 text-red-800'
  };
  
 

  const newComment = ref('');
  

  const formatDate = (date) => {
    const now = new Date();
    const diffInMs = now - date;
    const diffInSec = Math.floor(diffInMs / 1000);
    const diffInMin = Math.floor(diffInSec / 60);
    const diffInHours = Math.floor(diffInMin / 60);
    const diffInDays = Math.floor(diffInHours / 24);
  
    if (diffInDays > 0) {
      return diffInDays === 1 ? 'Yesterday' : `${diffInDays} days ago`;
    } else if (diffInHours > 0) {
      return `${diffInHours} ${diffInHours === 1 ? 'hour' : 'hours'} ago`;
    } else if (diffInMin > 0) {
      return `${diffInMin} ${diffInMin === 1 ? 'minute' : 'minutes'} ago`;
    } else {
      return 'Just now';
    }
  };
  
 
 
  
  // Toggle reply form visibility
  const toggleReplyForm = (comment) => {
    comment.showReplyForm = !comment.showReplyForm;
    if (!comment.showReplyForm) {
      comment.replyText = '';
      comment.replyingTo = null;
    }
  };
  


const addComment = async () => {
  if (!newComment.value.trim()) return;

  const token = window.localStorage.getItem('token');
      if (!token) {
        console.error('Authentication token is missing.');
        return;
      }
      const config = {
        headers: {
          Authorization: 'Bearer ' + token
        }
      };

  try {
    const response = await axios.post('/admin/ticket/comments', {
      comment: newComment.value,
      ticketId: dataId.value, 
    },config);

    fetchDataInfoList();

    newComment.value = '';
  } catch (error) {
    console.error('Failed to add comment:', error);
  }
};
  
 

const addReply = async (comment) => {
  if (!comment.replyText?.trim()) return;
  const token = window.localStorage.getItem('token');
      if (!token) {
        console.error('Authentication token is missing.');
        return;
      }
      const config = {
        headers: {
          Authorization: 'Bearer ' + token
        }
      };

  try {
    const response = await axios.post('/admin/ticket/comments/reply', {
      parent_id: comment.id,
      content: comment.replyText,
    },config);

      fetchDataInfoList();

    comment.replies.push(reply);
    comment.showReplyForm = false;
    comment.replyText = '';
    comment.replyingTo = null;

    // Auto-expand replies if there are now more than 2
    if (comment.replies.length > 2) {
      comment.showAllReplies = true;
    }
  } catch (error) {
    console.error('Failed to add reply:', error);
    
  }
};

  
  // Reply to a reply
  const replyToReply = (comment, replyingTo) => {
    comment.showReplyForm = true;
    comment.replyingTo = replyingTo;
    comment.replyText = `@${replyingTo.user.name} `;
    
    // Scroll to the reply form
    setTimeout(() => {
      const replyForm = document.querySelector(`#reply-form-${comment.id}`);
      if (replyForm) {
        replyForm.scrollIntoView({ behavior: 'smooth' });
        const textarea = replyForm.querySelector('textarea');
        if (textarea) {
          textarea.focus();
        }
      }
    }, 100);
  };
  const fetchAllData = async () => {
        await fetchDataInfoList();
   
    
  };
    onBeforeMount(fetchAllData);
  </script>
  
  <style>
  :root {
    --color-primary: #6366f1;
    --color-primary-hover: #4f46e5;
  }
  
  .bg-primary {
    background-color: var(--color-primary);
  }
  
  .bg-primary\/90 {
    background-color: rgba(99, 102, 241, 0.9);
  }
  
  .text-primary {
    color: var(--color-primary);
  }
  
  .border-primary {
    border-color: var(--color-primary);
  }
  
  .ring-primary {
    --tw-ring-color: var(--color-primary);
  }
  
  .focus-within\:border-primary:focus-within {
    border-color: var(--color-primary);
  }
  
  .focus-within\:ring-primary:focus-within {
    --tw-ring-color: var(--color-primary);
  }
  </style>
  