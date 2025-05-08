// src/services/echo.js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

export const initializeEcho = () => {
  // Clear any existing Pusher/Echo instances
  window.Pusher = Pusher;
  
  if (window.Echo) {
    // Clean up existing Echo instance
    if (window.Echo.connector && window.Echo.connector.channels) {
      Object.keys(window.Echo.connector.channels.channels || {}).forEach(channel => {
        window.Echo.leave(channel);
      });
    }
    // Don't null out Echo as this can cause issues when components are trying to use it
    // Just disconnect and let it be reinitialized
    if (window.Echo.connector && window.Echo.connector.pusher) {
      window.Echo.connector.pusher.disconnect();
    }
  }
  
  const token = localStorage.getItem('token');
  if (!token) {
    console.error('No authentication token found');
    return null;
  }
  
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  
  try {
    window.Echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
      wsPort: parseInt(import.meta.env.VITE_REVERB_PORT) || 8080,
      wsPath: import.meta.env.VITE_REVERB_PATH || '',
      forceTLS: false,
      disableStats: true,
      enabledTransports: ['ws', 'wss'],
      auth: {
        headers: {
          'Authorization': `Bearer ${token}`,
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      },
      authEndpoint: '/api/broadcasting/auth',
    });
    
    // Ensure connection is robust with auto-reconnect
    window.Echo.connector.pusher.config.enabledTransports = ['ws', 'wss', 'xhr_streaming', 'xhr_polling'];
    window.Echo.connector.pusher.config.autoReconnect = true;
    window.Echo.connector.pusher.config.activityTimeout = 30000; // 30 seconds
    window.Echo.connector.pusher.config.pongTimeout = 5000; // 5 seconds
    
    console.log('Echo initialized successfully');
    return window.Echo;
  } catch (error) {
    console.error('Failed to initialize Echo:', error);
    return null;
  }
};

// Setup listeners for debug purposes
export const setupDebugListeners = () => {
  if (!window.Echo) return;
  
  window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Successfully connected to Reverb.');
    // Connection state for debugging
    window.echoConnected = true;
    
    // Notify components of connection
    window.dispatchEvent(new CustomEvent('echo:connected'));
  });
  
  window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('Disconnected from Reverb.');
    window.echoConnected = false;
    
    // Notify components of disconnection
    window.dispatchEvent(new CustomEvent('echo:disconnected'));
    
    // Auto reconnect after disconnection
    setTimeout(() => {
      if (!window.echoConnected) {
        console.log('Attempting to reconnect to Reverb...');
        initializeEcho();
      }
    }, 5000);
  });
  
  window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Reverb connection error:', err);
    window.echoConnected = false;
    
    // Notify components of error
    window.dispatchEvent(new CustomEvent('echo:error', { detail: err }));
  });
  
  // Listen for subscription errors
  window.Echo.connector.pusher.connection.bind('subscription_error', (err) => {
    console.error('Reverb subscription error:', err);
    
    // Notify components
    window.dispatchEvent(new CustomEvent('echo:subscription_error', { detail: err }));
  });
};

// Helper function to check if Echo is properly connected
export const isEchoConnected = () => {
  return window.Echo && 
         window.Echo.connector && 
         window.Echo.connector.pusher && 
         window.Echo.connector.pusher.connection.state === 'connected';
};

// Cleaner function to ensure we're properly subscribed to a channel
export const ensureChannelSubscription = (channelName, eventName, callback) => {
  if (!window.Echo) {
    console.error('Echo not initialized when trying to subscribe to channel:', channelName);
    return null;
  }
  
  // First try to leave the channel to avoid duplicate subscriptions
  try {
    window.Echo.leave(channelName);
  } catch (err) {
    // Channel might not exist yet, which is fine
  }
  
  // Now subscribe to the channel
  const channel = window.Echo.private(channelName);
  
  // Debug the subscription
  console.log(`Subscribing to channel: ${channelName} for event: ${eventName}`);
  
  return channel.listen(eventName, (data) => {
    console.log(`Received event ${eventName} on channel ${channelName}:`, data);
    callback(data);
  });
};

export default {
  initializeEcho,
  setupDebugListeners,
  isEchoConnected,
  ensureChannelSubscription
};