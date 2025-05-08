<template>
  <div class="flex h-[85vh] bg-gray-50">
    <!-- User Sidebar -->
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
      <div class="p-4 border-b border-gray-200">
        <div class="relative">
          <SearchIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
          <input 
            type="text" 
            placeholder="Search users..." 
            class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
            v-model="searchQuery"
          />
        </div>
      </div>
      
      <div class="flex px-4 py-2 border-b border-gray-200 text-sm">
        <button 
          @click="activeFilter = 'all'" 
          :class="['px-3 py-1 rounded-md', activeFilter === 'all' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100']"
        >
          All
        </button>
        <!-- <button 
          @click="activeFilter = 'unread'" 
          :class="['px-3 py-1 rounded-md ml-2', activeFilter === 'unread' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100']"
        >
          Unread
        </button>
        <button 
          @click="activeFilter = 'assigned'" 
          :class="['px-3 py-1 rounded-md ml-2', activeFilter === 'assigned' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100']"
        >
          Assigned
        </button> -->
      </div>
      
      <div class="flex-1 overflow-y-auto">
        <div 
          v-for="user in filteredUsers" 
          :key="user.id" 
          @click="selectUser(user)"
          :class="[
            'p-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer',
            selectedUser && selectedUser.id === user.id ? 'bg-gray-100' : ''
          ]"
        >
          <div class="flex items-start">
            <div class="relative">
              <img :src="user.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name)" alt="User avatar" class="w-10 h-10 rounded-full object-cover" />
              <span 
                :class="[
                  'absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white',
                  user.status === 'online' ? 'bg-green-500' : 
                  user.status === 'away' ? 'bg-yellow-500' : 'bg-gray-400'
                ]"
              ></span>
            </div>
            <div class="ml-3 flex-1">
              <div class="flex justify-between items-start">
                <h3 class="font-medium text-gray-900">{{ user.name }}</h3>
                <span class="text-xs text-gray-500">{{ user.lastMessageTime || '' }}</span>
              </div>
              <p class="text-sm text-gray-500 truncate">{{ user.lastMessage || 'No messages yet' }}</p>
              <div class="flex items-center mt-1">
                <span v-if="user.unread_count > 0" class="bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {{ user.unread_count }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="p-4 border-t border-gray-200">
        <div class="flex items-center">
          <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Admin avatar" class="w-8 h-8 rounded-full" />
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">Support Team</p>
            <p class="text-xs text-gray-500">Admin</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col w-[600px]">
      <!-- Header -->
      <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <button class="p-1 rounded-full hover:bg-gray-100 md:hidden">
            <MenuIcon class="h-5 w-5 text-gray-500" />
          </button>
          <div v-if="selectedUser">
            <div class="flex items-center">
              <img :src="selectedUser.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(selectedUser.name)" alt="User avatar" class="w-8 h-8 rounded-full mr-2" />
              <div>
                <h1 class="font-semibold text-gray-900">{{ selectedUser.name }}</h1>
                <div class="flex items-center">
                  <span 
                    :class="[
                      'h-2 w-2 rounded-full mr-2',
                      selectedUser.status === 'online' ? 'bg-green-500' : 'bg-gray-400'
                    ]"
                  ></span>
                  <span class="text-sm text-gray-500">
                    {{ selectedUser.status === 'online' ? 'Online' : 'Offline' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-gray-500">
            Select a user to start chatting
          </div>
        </div>
      </header>

      <template v-if="selectedUser">
        <!-- Chat Messages -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4" ref="chatContainer">
          <div v-for="(message, index) in messages" :key="index" 
               :class="{'flex justify-end': message.isAdmin, 'flex justify-start': !message.isAdmin}">
            <div :class="{
              'bg-white border border-gray-200 rounded-lg p-3 max-w-xs sm:max-w-md shadow-sm': !message.isAdmin,
              'bg-primary text-white rounded-lg p-3 max-w-xs sm:max-w-md': message.isAdmin
            }">
              <p>{{ message.text }}</p>
              <div :class="{
                'text-xs text-gray-500 mt-1 text-right': !message.isAdmin,
                'text-xs text-primary-foreground mt-1 text-right': message.isAdmin
              }">
                {{ message.time }}
                <span v-if="message.isAdmin">
                  <CheckIcon v-if="message.status === 'read'" class="inline h-4 w-4 ml-1" />
                  <CheckCheckIcon v-else-if="message.status === 'delivered'" class="inline h-4 w-4 ml-1" />
                  <ClockIcon v-else class="inline h-4 w-4 ml-1" />
                </span>
              </div>
            </div>
          </div>
          <div v-if="isTyping" class="flex justify-start">
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
              <div class="flex space-x-1">
                <div class="h-2 w-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="h-2 w-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="h-2 w-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Chat Input -->
        <div class="bg-white border-t border-gray-200 p-4">
          <div class="flex items-end space-x-2">
            <div class="flex-1 bg-gray-100 rounded-lg p-2">
              <textarea 
                v-model="newMessage" 
                @keydown.enter.prevent="sendMessage"
                placeholder="Type your message..." 
                class="w-full bg-transparent outline-none resize-none text-gray-700"
                rows="2"
              ></textarea>
            </div>
            <button 
              @click="sendMessage" 
              :disabled="!newMessage.trim() || !selectedUser"
              :class="[
                'p-3 rounded-full',
                newMessage.trim() && selectedUser ? 'bg-primary text-white' : 'bg-gray-200 text-gray-400'
              ]"
            >
              <SendIcon class="h-5 w-5 cursor-pointer" />
            </button>
          </div>
        </div>
      </template>
      
      <div v-else class="flex-1 flex items-center justify-center bg-gray-50">
        <div class="text-center p-6">
          <MessageSquareIcon class="h-12 w-12 text-gray-300 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900">No conversation selected</h3>
          <p class="text-gray-500 mt-1">Choose a user from the sidebar to start chatting</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, computed, onMounted, nextTick, watch, onBeforeUnmount } from 'vue';
import { 
  Search as SearchIcon,
  Phone as PhoneIcon,
  UserPlus as UserPlusIcon,
  MoreVertical as MoreVerticalIcon,
  Check as CheckIcon,
  CheckCheck as CheckCheckIcon,
  Clock as ClockIcon,
  Send as SendIcon,
  Menu as MenuIcon,
  MessageSquare as MessageSquareIcon
} from 'lucide-vue-next';
import { initializeEcho, setupDebugListeners } from '../../echo';

// State
const newMessage = ref('');
const isTyping = ref(false);
const chatContainer = ref(null);
const selectedUser = ref(null);
const searchQuery = ref('');
const activeFilter = ref('all');
const messages = ref([]);
const users = ref([]);
const echo = ref(null);

// Computed properties
const filteredUsers = computed(() => {
  let result = users.value;
  
  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(user => 
      user.name.toLowerCase().includes(query)
    );
  }
  
  // Apply tab filter
  if (activeFilter.value === 'unread') {
    result = result.filter(user => user.unread_count > 0);
  } else if (activeFilter.value === 'assigned') {
    // In a real app, this would filter for users assigned to the current agent
    result = result.filter(user => user.assigned === true);
  }
  
  return result;
});

const setupReverbForUser = (userId = null) => {
  if (!window.Echo) {
    console.error('Echo not initialized');
    return;
  }

  // Clean up any existing listeners
  if (window.Echo.connector?.channels) {
    Object.keys(window.Echo.connector.channels).forEach(channel => {
      if (channel.startsWith('private-chat.')) {
        window.Echo.leave(channel);
      }
    });
  }
  
  console.log(`Setting up chat channels ${userId ? 'for user ' + userId : 'for admin'}`);
  
  // Always listen on the admin channel
  const adminChannel = window.Echo.private('chat.admin');
  
  adminChannel
    .listen('.MessageSent', (data) => {
      console.log('Message received:', data);
      handleIncomingMessage(data);
      
      // Update user list if this is a new message
      if (data.sender_id && (!selectedUser.value || data.sender_id !== selectedUser.value.id)) {
        updateUserUnreadCount(data.sender_id);
      }
    })
    .listen('.MessageStatus', (data) => {
      updateMessageStatus(data);
    });
};

const updateUserUnreadCount = (userId) => {
  const userIndex = users.value.findIndex(u => u.id === userId);
  if (userIndex !== -1) {
    users.value[userIndex].unread_count = (users.value[userIndex].unread_count || 0) + 1;
  }
};

const updateMessageStatus = (data) => {
  const index = messages.value.findIndex(m => m.id === data.message_id);
  if (index !== -1) {
    messages.value[index].status = data.status;
  }
};
// Modify the handleIncomingMessage function 
const handleIncomingMessage = (data) => {
  // Process incoming message and update UI
  const newMsg = {
    id: data.id || `temp-${Date.now()}`,
    text: data.text,
    sender_id: data.sender_id,
    receiver_id: data.receiver_id,
    time: data.time || getCurrentFormattedTime(),
    isAdmin: data.is_admin,
    status: data.status || 'delivered'
  };
  
  // Add to messages if this is the current conversation
  if (selectedUser.value && (data.sender_id === selectedUser.value.id || data.receiver_id === selectedUser.value.id)) {
    // Check if we already have this message (prevent duplicates)
    const existingMsg = messages.value.find(m => m.id === newMsg.id);
    if (!existingMsg) {
      messages.value.push(newMsg);
      scrollToBottom();
    }
  }
};

const selectUser = async (user) => {
  try {
    // Clear any existing messages first
    messages.value = [];
    
    selectedUser.value = user;
    // Reset unread count
    user.unread_count = 0;
    
    const token = localStorage.getItem('token');
    const response = await axios.get(`/admin/chat/messages/${user.id}`, {
      headers: { Authorization: `Bearer ${token}` },
      params: {
        _: new Date().getTime() // Cache buster
      }
    });
    
    messages.value = response.data;
    scrollToBottom();
    
    // Set up Echo for this user
    setupReverbForUser(user.id);
    
    // Mark messages as read

  } catch (error) {
    console.error('Error fetching messages:', error);
    // Optionally show error to user
  }
};



const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedUser.value) return;
  
  const messageText = newMessage.value.trim();
  newMessage.value = '';
  
  // Optimistic update for UI
  const tempMessage = {
    id: `temp-${Date.now()}`,
    text: messageText,
    time: getCurrentFormattedTime(),
    isAdmin: true,
    status: 'sending'
  };
  
  messages.value.push(tempMessage);
  scrollToBottom();
  
  try {
    const token = localStorage.getItem('token');
    const response = await axios.post('/admin/chat/send', {
      receiver_id: selectedUser.value.id,
      text: messageText
    }, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    // Replace temp message with actual message from response
    const index = messages.value.findIndex(m => m.id === tempMessage.id);
    if (index !== -1) {
      messages.value.splice(index, 1, response.data);
    }
    

    const user = users.value.find(u => u.id === selectedUser.value.id);
    if (user) {
      user.lastMessage = messageText;
      user.lastMessageTime = response.data.time;
    }
  } catch (error) {
    console.error('Failed to send message:', error);
    // Update status of temp message to show error
    const index = messages.value.findIndex(m => m.id === tempMessage.id);
    if (index !== -1) {
      messages.value[index].status = 'failed';
    }
  }
};

// Initialize the chat system with proper lifecycle hook placement
const initializeChat = async () => {
  // Store the polling interval reference
  const pollingInterval = ref(null);
  
  try {
    // Initialize Echo only if it's not already initialized
    if (!window.Echo) {
      window.Echo = initializeEcho();
      setupDebugListeners();
    }
    
    setupReverbForUser();
    await fetchUserList();
    
    // Set up polling as a fallback - moved outside the try block
    pollingInterval.value = setInterval(async () => {
      await fetchUserList();
    }, 60000);
    
  } catch (error) {
    console.error('Failed to initialize chat:', error);
  }
  
  // Cleanup function to be called in onBeforeUnmount
  return () => {
    if (pollingInterval.value) {
      clearInterval(pollingInterval.value);
    }
    
    // Clean up Echo listeners
    if (window.Echo?.connector?.channels?.channels) {
      Object.keys(window.Echo.connector.channels.channels).forEach(channel => {
        window.Echo.leave(channel);
      });
    }
  };
};

// Lifecycle hooks
const cleanupChat = ref(null);

onMounted(async () => {
  cleanupChat.value = await initializeChat();
});

onBeforeUnmount(() => {
  if (cleanupChat.value) {
    cleanupChat.value();
  }
});

// Update the fetchUserList function to properly set up channels
const fetchUserList = async () => {
  try {
    const token = localStorage.getItem('token');
    const response = await axios.get('/admin/chat/users', {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    users.value = response.data;
    
    // Set up channels if we have users but none selected yet
    if (users.value.length > 0) {
      if (!selectedUser.value) {
        selectUser(users.value[0]);
      } else {
        // Make sure we're still listening on the correct channels
        setupReverbForUser(selectedUser.value.id);
      }
    }
  } catch (error) {
    console.error('Error fetching users:', error);
  }
};

const getCurrentFormattedTime = () => {
  const now = new Date();
  const hours = now.getHours() % 12 || 12;
  const minutes = now.getMinutes().toString().padStart(2, '0');
  const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
  return `${hours}:${minutes} ${ampm}`;
};

const scrollToBottom = () => {
  nextTick(() => {
    if (chatContainer.value) {
      chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
  });
};

// Initialize the chat system
// In the admin component (paste.txt), replace or add these lifecycle hooks

// Initialize the chat system with improved error handling

// Lifecycle hooks
onMounted(async () => {
  await initializeChat();
  setupReverbForUser();
  
  // Set up polling as a fallback
  const pollingInterval = setInterval(async () => {
    // Only use polling as fallback if WebSocket is not connected
    if (!window.echoConnected) {
      console.log('WebSocket seems down, using polling fallback');
      await fetchUserList();
      
      // If we have a selected user, refresh messages for them
      if (selectedUser.value) {
        try {
          const token = localStorage.getItem('token');
          const response = await axios.get(`/admin/chat/messages/${selectedUser.value.id}`, {
            headers: { Authorization: `Bearer ${token}` }
          });
          
          // Carefully merge new messages, avoiding duplicates
          const existingIds = new Set(messages.value.map(m => m.id));
          const newMessages = response.data.filter(m => !existingIds.has(m.id));
          
          if (newMessages.length > 0) {
            messages.value.push(...newMessages);
            scrollToBottom();
          }
        } catch (error) {
          console.error('Error fetching messages in polling:', error);
        }
      }
    }
  }, 30000); // Every 30 seconds
  
  // Clean up on unmount
  onBeforeUnmount(() => {
    clearInterval(pollingInterval);
    setupReverbForUser();
    handleIncomingMessage();
    // Clean up Echo listeners
    if (window.Echo) {
      try {
        if (window.Echo.connector && window.Echo.connector.channels) {
          Object.keys(window.Echo.connector.channels || {}).forEach(channel => {
            window.Echo.leave(channel);
          });
        }
        window.Echo.disconnect();
      } catch (e) {
        console.error('Error cleaning up Echo:', e);
      }
    }
  });
});
</script>

<style>

.scrollbar-hide::-webkit-scrollbar {
  display: none;
}


.scrollbar-hide {
  -ms-overflow-style: none;  
  scrollbar-width: none;
}

:root {
  --primary: #7c3aed;
  --primary-foreground: #f8fafc;
}

.bg-primary {
  background-color: var(--primary);
}

.text-primary {
  color: var(--primary);
}

.text-primary-foreground {
  color: var(--primary-foreground);
}
</style>