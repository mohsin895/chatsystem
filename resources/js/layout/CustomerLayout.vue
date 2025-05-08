<template>
   <div class="layout">
      <nav-bar />
      <div class="erp-body flex min-h-screen  pt-11">
        <div class="right-side hidden lg:block bg-rightBarColor" >
          <header-page />
        </div>
        <div class="left-side bg-bodyColor  w-full">
          <RouterView />
        </div>
        <div class="fixed bottom-4 right-4 z-50 flex flex-col items-end">
  <div class="chat-widget">
    <!-- Chat toggle button with notification badge -->
    <button @click="toggleChat" class="chat-toggle">
      <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
      </svg>
    </button>
    
    <!-- Chat popup -->
    <div v-if="isOpen" class="chat-popup">
      <!-- Chat header -->
      <div class="chat-header">
        <div class="agent-info">
          <div class="agent-avatar">
            <img :src="agent.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(agent.name)" alt="Support Agent">
            <span class="status-indicator online"></span>
          </div>
          <div class="agent-details">
            <h3>{{ agent.name || 'Support Agent' }}</h3>
            <p>{{ connectionStatus === 'connected' ? 'Online' : 'Connecting...' }}</p>
          </div>
        </div>
        <button @click="toggleChat" class="close-button">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
      
      <!-- Chat messages -->
      <div ref="messagesContainer" class="chat-messages">
        <div v-if="messages.length === 0" class="empty-chat">
          <p>Start a conversation with our support team!</p>
        </div>
        
        <div v-for="(message, index) in messages" :key="index" :class="['message', message.sender]">
          <div class="message-content">
            {{ message.text }}
          </div>
          <div class="message-meta">
            <span class="time">{{ message.time }}</span>
            
            <!-- Message status indicators for user messages -->
            <span v-if="message.sender === 'user'" class="status-indicator">
              <svg v-if="message.status === 'sending'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <svg v-else-if="message.status === 'sent'" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              <svg v-else-if="message.status === 'failed'" @click="retryMessage(message)" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="retry-icon">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
            </span>
          </div>
        </div>
        
        <!-- Typing indicator -->
        <div v-if="isTyping" class="typing-indicator">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>
      
      <!-- Message input -->
      <div class="chat-input">
        <textarea 
          v-model="newMessage" 
          @keydown="handleKeyDown"
          placeholder="Type your message..."
          rows="1"
        ></textarea>
        <button @click="sendMessage" :disabled="!newMessage.trim()">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="22" y1="2" x2="11" y2="13"></line>
            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
          </svg>
        </button>
      </div>
    </div>
  </div>
</div>
       
      </div>
        <footer-page />
      </div>
</template>


<script setup>
import axios from 'axios';
import { ref, onMounted, nextTick, watch, onBeforeUnmount } from 'vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { useRouter } from 'vue-router';
import HeaderPage from '@/layout/customerRightBar.vue';
  import FooterPage from '@/layout/Footer.vue';
  import NavBar from '@/layout/Navbar.vue';
  

const router = useRouter();
  
// Check authentication
const token = localStorage.getItem("token");
if (!token) {
  router.push({ name: 'login' }).catch(err => {
    console.error(err);
  });
}

// Chat state
const isOpen = ref(false);
const newMessage = ref('');
const isTyping = ref(false);
const messagesContainer = ref(null);
const unreadCount = ref(0);
const connectionStatus = ref('disconnected'); // Track connection status

// Support agent info
const agent = ref({});

// Messages array
const messages = ref([]);

// Initialize Echo/Reverb for WebSocket communication
const initializeEcho = () => {
  const token = localStorage.getItem('token');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  
  // Initialize Pusher for Reverb compatibility
  window.Pusher = Pusher;
  
  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT) || 8080,
    forceTLS: false,
    // forceTLS: window.location.protocol === 'https:',
    enabledTransports: ['ws', 'wss'],
    auth: {
      headers: {
        'Authorization': `Bearer ${token}`,
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    },
    authEndpoint: '/api/broadcasting/auth'
  });

  // Log connection status
  window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Connected to Reverb WebSocket');
    connectionStatus.value = 'connected';
  });

  window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Disconnected from Reverb WebSocket');
    connectionStatus.value = 'disconnected';
  });

  window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Reverb connection error:', err);
    connectionStatus.value = 'error';
  });

  return window.Echo;
};

// Fetch initial chat data
const fetchChatData = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) return;
    
    const response = await axios.get(`/chat/messages`, {
      headers: { Authorization: 'Bearer ' + token }
    });
    
    messages.value = response.data.messages || []; 
    agent.value = response.data.admin || {};
    
    // Count unread messages
    unreadCount.value = messages.value.filter(msg => 
      msg.sender === 'agent' && !msg.read_at 
    ).length;
    console.log(unreadCount);
    
    console.log('Fetched messages:', messages.value.length);
  } catch (error) {
    console.error('Error fetching chat data:', error);
  }
};

// Set up WebSocket listeners for real-time messaging
const setupReverbForUser = () => {
  try {
    const userData = JSON.parse(localStorage.getItem('authInfo'));
    if (!userData?.id) {
      console.error('No user ID found in authInfo');
      return;
    }
    
    const userId = userData.id;
    
    
    // Clean up any existing listeners to avoid duplicates
    if (window.Echo) {
      try {
        window.Echo.leave(`chat.user.${userId}`);
      } catch (err) {
        console.log('No existing channel to leave');
      }
    } else {
      console.error('Echo not initialized');
      return;
    }

    // Listen for messages on the user's private channel
    window.Echo.private(`chat.user.${userId}`)
      .listen('.MessageSent', (data) => {
        console.log('Received message via WebSocket:', data);
        handleIncomingMessage(data);
      })
      .error((error) => {
        console.error('Error subscribing to channel:', error);
      });
      
    console.log(`Listening on channel: chat.user.${userId}`);
  } catch (error) {
    console.error('Error setting up Reverb:', error);
  }
};

// Handle incoming messages received via WebSocket
const handleIncomingMessage = (data) => {
  const userData = JSON.parse(localStorage.getItem('authInfo'));
  
  // Don't add our own messages twice (they're already added optimistically)
  if (data.sender_id === userData?.id && data.is_admin === false) {
    console.log('Skipping own message from WebSocket');
    return;
  }
  
  const newMsg = {
    id: data.id,
    text: data.text,
    sender: data.is_admin ? 'agent' : 'user',
    sender_id: data.sender_id,
    receiver_id: data.receiver_id,
    timestamp: data.timestamp || new Date().toISOString(),
    time: data.time || formatTime(new Date()),
    read: isOpen.value // Mark as read if chat is open
  };
  
  console.log('Adding new message to chat:', newMsg);
  messages.value.push(newMsg);
  
  // Update unread count if chat is closed
  if (!isOpen.value && data.is_admin) {
    unreadCount.value++;
  }
  
  scrollToBottom();
};

// Toggle chat visibility
const toggleChat = () => {
  isOpen.value = !isOpen.value;
  
  if (isOpen.value) {
    // Reset unread count when opening chat
    unreadCount.value = 0;
    
    // Mark messages as read
    messages.value.forEach(msg => {
      if (msg.sender === 'agent' && !msg.read) {
        msg.read = true;
      }
    });
    
    // Notify server that messages have been read

    // Scroll to bottom
    nextTick(scrollToBottom);
  }
};


// Format time display
const formatTime = (timestamp) => {
  const date = new Date(timestamp);
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Send a new message
const sendMessage = async () => {
  if (!newMessage.value.trim()) return;

  const token = localStorage.getItem('token');
  if (!token) return;

  const messageText = newMessage.value.trim();
  newMessage.value = ''; // Clear input immediately

  try {
    // Add message to UI immediately (optimistic update)
    const tempMessage = {
      id: `temp-${Date.now()}`,
      text: messageText,
      sender: 'user',
      timestamp: new Date().toISOString(),
      time: formatTime(new Date()),
      status: 'sending'
    };
    
    messages.value.push(tempMessage);
    scrollToBottom();

    // Send to server
    const response = await axios.post('/chat/send', {
      text: messageText
    }, {
      headers: { Authorization: 'Bearer ' + token }
    });

    // Update with server response
    const index = messages.value.findIndex(m => m.id === tempMessage.id);
    if (index !== -1) {
      // Replace temp message with real one from server
      messages.value.splice(index, 1, {
        ...response.data,
        sender: 'user',
        status: 'sent',
        time: formatTime(new Date(response.data.timestamp || Date.now()))
      });
    }
    
    console.log('Message sent successfully');
  } catch (error) {
    console.error('Error sending message:', error);
    
    // Mark message as failed
    const index = messages.value.findIndex(m => m.text === messageText && m.sender === 'user');
    if (index !== -1) {
      messages.value[index].status = 'failed';
    }
  }
};

// Handle enter key in message input
const handleKeyDown = (event) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    sendMessage();
  }
};

// Scroll to bottom of chat
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
    }
  });
};

// Retry sending failed messages
const retryMessage = async (message) => {
  // Remove the failed message
  const index = messages.value.findIndex(m => m.id === message.id);
  if (index !== -1) {
    messages.value.splice(index, 1);
  }
  
  // Set the message text and send again
  newMessage.value = message.text;
  await sendMessage();
};

// Initialize when component mounts
onMounted(async () => {
  try {
    // Initialize Echo/Reverb
    initializeEcho();
    
    // Fetch initial messages
    await fetchChatData();
    
    // Set up WebSocket listeners
    setupReverbForUser();
    
    // Scroll to bottom of messages
    scrollToBottom();
    
    // Set up polling as fallback
    const pollingInterval = setInterval(async () => {
      console.log('Polling for new messages');
      
      await fetchChatData();
    }, 3000); // Every 30 seconds
    
    // Clean up on component unmount
    onBeforeUnmount(() => {
      clearInterval(pollingInterval);
      
      // Clean up WebSocket listeners
      const userData = JSON.parse(localStorage.getItem('authInfo'));
      if (userData?.id && window.Echo) {
        window.Echo.leave(`chat.user.${userData.id}`);
      }
    });
  } catch (error) {
    console.error('Chat initialization error:', error);
  }
});

// Check WebSocket connection status and reconnect if needed
watch(connectionStatus, (newStatus) => {
  if (newStatus === 'disconnected' || newStatus === 'error') {
    console.log('Trying to reconnect WebSocket...');
    setTimeout(() => {
      scrollToBottom();
      // initializeEcho();
      setupReverbForUser();
    }, 5000); // Try to reconnect after 5 seconds
  }
});
</script>


<style scoped>
.chat-widget {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 999;
}

.chat-toggle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background-color: #4f46e5;
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: relative;
  transition: all 0.3s ease;
}

.chat-toggle:hover {
  background-color: #4338ca;
  transform: scale(1.05);
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #ef4444;
  color: white;
  border-radius: 50%;
  width: 22px;
  height: 22px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
}

.chat-popup {
  position: absolute;
  bottom: 80px;
  right: 0;
  width: 350px;
  height: 500px;
  background-color: white;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.chat-header {
  padding: 16px;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.agent-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.agent-avatar {
  position: relative;
  width: 40px;
  height: 40px;
}

.agent-avatar img {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  object-fit: cover;
}

.status-indicator {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  position: absolute;
  bottom: 0;
  right: 0;
  border: 2px solid white;
}

.status-indicator.online {
  background-color: #10b981;
}

.agent-details h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.agent-details p {
  margin: 0;
  font-size: 12px;
  color: #6b7280;
}

.close-button {
  background: none;
  border: none;
  cursor: pointer;
  color: #9ca3af;
}

.close-button:hover {
  color: #4b5563;
}

.chat-messages {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.empty-chat {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #9ca3af;
  text-align: center;
  font-style: italic;
}

.message {
  max-width: 80%;
  padding: 10px 14px;
  border-radius: 18px;
  word-break: break-word;
}

.message.user {
  align-self: flex-end;
  background-color: #4f46e5;
  color: white;
  border-bottom-right-radius: 4px;
}

.message.agent {
  align-self: flex-start;
  background-color: #f3f4f6;
  color: #1f2937;
  border-bottom-left-radius: 4px;
}

.message-meta {
  font-size: 11px;
  margin-top: 4px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
}

.message.user .message-meta {
  color: rgba(255, 255, 255, 0.7);
}

.message.agent .message-meta {
  color: #9ca3af;
  justify-content: flex-start;
}

.retry-icon {
  cursor: pointer;
}

.typing-indicator {
  align-self: flex-start;
  padding: 12px 16px;
  background-color: #f3f4f6;
  border-radius: 18px;
  display: flex;
  align-items: center;
  gap: 4px;
}

.typing-indicator span {
  width: 8px;
  height: 8px;
  background-color: #9ca3af;
  border-radius: 50%;
  display: inline-block;
  animation: bounce 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(1) {
  animation-delay: -0.32s;
}

.typing-indicator span:nth-child(2) {
  animation-delay: -0.16s;
}

@keyframes bounce {
  0%, 80%, 100% { transform: scale(0); }
  40% { transform: scale(1); }
}

.chat-input {
  padding: 12px 16px;
  border-top: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  gap: 12px;
}

.chat-input textarea {
  flex: 1;
  padding: 10px 16px;
  border-radius: 24px;
  border: 1px solid #e5e7eb;
  resize: none;
  font-family: inherit;
  font-size: 14px;
  outline: none;
  max-height: 100px;
}

.chat-input textarea:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
}

.chat-input button {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background-color: #4f46e5;
  color: white;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.chat-input button:hover {
  background-color: #4338ca;
}

.chat-input button:disabled {
  background-color: #e5e7eb;
  color: #9ca3af;
  cursor: not-allowed;
}
</style>