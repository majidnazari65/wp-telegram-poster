# WP Telegram Poster

A WordPress plugin to automatically publish posts from any public post type to a Telegram channel or group using a Cloudflare Worker. It supports Unicode hashtags (e.g., Persian) and is optimized for users in regions with restricted Telegram access.

## Features

- Publish posts from any public post type (posts, pages, custom post types like WooCommerce products) to Telegram.
- Automatically convert categories and taxonomies to hashtags, supporting Unicode characters (e.g., Persian).
- Configurable settings for secret key, Cloudflare Worker URL, Telegram Chat ID, and custom "Read More" button text.
- Persian language support with included translation files.
- No external server required; uses free Cloudflare Workers to bypass Telegram restrictions.
- Escapes Markdown characters in hashtags to prevent Telegram API errors.

## Prerequisites

- WordPress 5.0 or higher.
- A Telegram bot token (obtained from BotFather).
- A Cloudflare account with a deployed Worker (see the [Cloudflare Worker](https://github.com/majidnazari65/wp-telegram-poster-cloudflare-worker) repository).
- Basic knowledge of WordPress administration and GitHub.

## Installation

1. **Download the Plugin**:

   - Clone or download this repository: `git clone https://github.com/yourusername/wp-telegram-poster.git`.
   - Alternatively, download the ZIP file and extract it.

2. **Upload to WordPress**:

   - Copy the `wp-telegram-poster` folder to `wp-content/plugins/` in your WordPress installation.
   - Or, upload the ZIP file via **Plugins &gt; Add New &gt; Upload Plugin** in the WordPress admin panel.

3. **Activate the Plugin**:

   - Go to **Plugins** in the WordPress admin panel and activate **WP Telegram Poster**.

## Configuration

1. **Plugin Settings**:

   - Navigate to **Settings &gt; WP Telegram Poster** in the WordPress admin panel.
   - Enter the following:
     - **Secret Key**: A secure key (must match the `SECRET` in your Cloudflare Worker).
     - **Cloudflare Worker URL**: The URL of your deployed Worker (e.g., `https://your-worker.your-subdomain.workers.dev`).
     - **Telegram Chat ID**: The ID of your Telegram channel or group (e.g., `@YourChannel` or a numeric group ID).
     - **Read More Button Text**: Custom text for the post link (e.g., "Read More" or "بیشتر بخوانید").
   - Save the settings.

2. **Ensure Cloudflare Worker is Set Up**:

   - Follow the instructions in the Cloudflare Worker repository to deploy the Worker and set its environment variables.

## Usage

1. Create or edit a post, page, or custom post type in WordPress.
2. Add categories, tags, or custom taxonomies (e.g., "دسته‌بندی نمونه" or "Test_Tag").
3. Publish the post.
4. In the post editor’s publish metabox, click the **Send to Telegram** button.
5. The post (title, excerpt, link, featured image, and hashtags) will be sent to your Telegram channel or group via the Cloudflare Worker.

## Support

- For issues, open a ticket on the GitHub Issues page.
- For professional installation or customization, contact us at info@aniltarah.com.

---

# WP Telegram Poster (فارسی)

افزونه‌ای برای وردپرس که امکان انتشار خودکار پست‌ها از هر نوع پست عمومی به کانال یا گروه تلگرام را با استفاده از Cloudflare Worker فراهم می‌کند. این افزونه از هشتگ‌های یونیکد (مانند فارسی) پشتیبانی می‌کند و برای کاربران در مناطقی که تلگرام محدود است بهینه شده است.

## ویژگی‌ها

- انتشار پست‌ها از هر نوع پست عمومی (پست‌ها، صفحات، پست‌تایپ‌های سفارشی مانند محصولات ووکامرس) به تلگرام.
- تبدیل خودکار دسته‌بندی‌ها و تاکسونومی‌ها به هشتگ با پشتیبانی از کاراکترهای یونیکد (مانند فارسی).
- تنظیمات قابل پیکربندی برای کلید مخفی، آدرس ورکر کلادفلیر، شناسه چت تلگرام، و متن دکمه "بیشتر بخوانید".
- پشتیبانی از زبان فارسی با فایل‌های ترجمه ارائه‌شده.
- بدون نیاز به سرور خارجی؛ از Cloudflare Workers رایگان برای دور زدن محدودیت‌های تلگرام استفاده می‌کند.
- فرار از کاراکترهای Markdown در هشتگ‌ها برای جلوگیری از خطاهای API تلگرام.

## پیش‌نیازها

- وردپرس نسخه 5.0 یا بالاتر.
- توکن ربات تلگرام (از BotFather دریافت کنید).
- حساب کلادفلیر با ورکر دیپلوی‌شده (به مخزن [Cloudflare Worker](https://github.com/majidnazari65/wp-telegram-poster-cloudflare-worker) مراجعه کنید).
- دانش پایه مدیریت وردپرس و گیت‌هاب.

## نصب

1. **دانلود افزونه**:

   - این مخزن را کلون کنید یا دانلود کنید: `git clone https://github.com/yourusername/wp-telegram-poster.git`.
   - یا فایل ZIP را دانلود کرده و استخراج کنید.

2. **آپلود به وردپرس**:

   - پوشه `wp-telegram-poster` را به `wp-content/plugins/` در نصب وردپرس خود کپی کنید.
   - یا از طریق **افزونه‌ها &gt; افزودن &gt; بارگذاری افزونه** در پنل مدیریت وردپرس، فایل ZIP را آپلود کنید.

3. **فعال‌سازی افزونه**:

   - به بخش **افزونه‌ها** در پنل مدیریت وردپرس بروید و **WP Telegram Poster** را فعال کنید.

## پیکربندی

1. **تنظیمات افزونه**:

   - به **تنظیمات &gt; WP Telegram Poster** در پنل مدیریت وردپرس بروید.
   - موارد زیر را وارد کنید:
     - **کلید مخفی**: یک کلید امن (باید با `SECRET` در ورکر کلادفلیر مطابقت داشته باشد).
     - **آدرس ورکر کلادفلیر**: آدرس ورکر دیپلوی‌شده (مثال: `https://your-worker.your-subdomain.workers.dev`).
     - **شناسه چت تلگرام**: شناسه کانال یا گروه تلگرام (مثال: `@YourChannel` یا یک ID عددی).
     - **متن دکمه بیشتر بخوانید**: متن سفارشی برای لینک پست (مثال: "بیشتر بخوانید").
   - تنظیمات را ذخیره کنید.

2. **اطمینان از راه‌اندازی ورکر کلادفلیر**:

   - دستورالعمل‌های موجود در مخزن Cloudflare Worker را برای دیپلوی ورکر و تنظیم متغیرهای محیطی دنبال کنید.

## استفاده

1. یک پست، صفحه، یا پست‌تایپ سفارشی در وردپرس ایجاد یا ویرایش کنید.
2. دسته‌بندی‌ها، تگ‌ها، یا تاکسونومی‌های سفارشی (مثل "دسته‌بندی نمونه" یا "تست\_ویژه") اضافه کنید.
3. پست را منتشر کنید.
4. در متاباکس انتشار ویرایشگر پست، روی دکمه **ارسال به تلگرام** کلیک کنید.
5. پست (عنوان، خلاصه، لینک، تصویر شاخص، و هشتگ‌ها) از طریق ورکر کلادفلیر به کانال یا گروه تلگرام شما ارسال می‌شود.

## پشتیبانی

- برای مشکلات، در صفحه Issues گیت‌هاب تیکت باز کنید.
- برای نصب حرفه‌ای یا سفارشی‌سازی، با ما از طریق info@aniltarah.com تماس بگیرید.
