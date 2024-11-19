# PHP Transparent Reverse Proxy
A lightweight, single-file PHP script to seamlessly route traffic between your browser and backend servers: ideal for remote development environments, flexible routing, and more.

## How to use
1. **Configure your web server**  
   If necessary, adjust your web server configuration (e.g., `.htaccess`) to forward all incoming requests to `proxy.php`.  
   Here's an example Apache configuration:
   
   ```
   RewriteEngine On
   RewriteCond %{REQUEST_FILENAME} -f [OR]
   RewriteCond %{REQUEST_FILENAME} -d
   RewriteRule ^ - [L]
   RewriteRule . proxy.php [L]
   ```
1. **Place the `proxy.php` file**  
   Copy the `proxy.php` file to your web server's document root directory.
1. **Customize the `$target` array**  
   Edit the `$target` array in `proxy.php` to specify the target server's address and port:
   
   ```
   $target = [
       'scheme' => 'http', // Protocol (http or https)
       'host' => 'your_backend_host', // Hostname or IP address
       'port' => 8000, // Port number
   ];
   ```
