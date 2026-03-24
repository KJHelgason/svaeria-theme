# Svaeria — Store Owner Guide

Welcome to your Svaeria store! The technical setup has been done for you. This guide covers the few things you need to do, plus how to manage your store day-to-day.

---

## Getting Started

### 1. Connect Stripe (Payment Processing)

This connects your bank account so you can receive payments from customers worldwide.

1. Go to **WooCommerce > Settings > Payments**
2. Find **Stripe** and click **Manage**
3. Click **Connect to Stripe**
4. Create a Stripe account or log in with an existing one
5. Follow the steps — you will need:
   - Your email address
   - Your bank account details (for payouts)
   - A form of ID for verification
6. Once connected, you're ready to accept credit cards, Apple Pay, and Google Pay

> Stripe pays out to your bank account on a rolling basis (typically every 2–3 business days).

### 2. Add Your Products

Go to **Products > Add New** for each item.

1. **Product name** — e.g., "Valkyria Ring — Sterling Silver"
2. **Description** — Tell the story: materials, inspiration, care instructions
3. **Short description** — 1–2 sentences (shown on the shop page)
4. **Price** — Enter in ISK with no decimals (e.g., 12900)
5. **Product image** — Your main photo (square, at least 800x800px)
6. **Product gallery** — Additional angles and detail shots
7. **Category** — Assign to Rings, Necklaces, Earrings, etc.

#### If a product comes in different sizes

1. Change **Product data** from "Simple product" to **Variable product**
2. Go to the **Attributes** tab
3. Add an attribute called "Size" with your values (e.g., S | M | L or ring sizes like 6 | 7 | 8)
4. Check **Used for variations**
5. Go to the **Variations** tab and click **Generate variations**
6. Set the price and stock quantity for each size

#### Tips for great product listings

- Use natural light for photos
- Show the jewelry being worn when possible
- Mention the material (sterling silver, 18k gold, etc.)
- Include dimensions or ring size guidance in the description

---

## Managing Your Store

### Processing Orders

1. Go to **WooCommerce > Orders**
2. New paid orders show up as **Processing**
3. Click an order to see what was purchased and the shipping address
4. Pack and ship the item
5. Change the order status to **Completed** and click **Update**
6. The customer automatically gets an email that their order has shipped

### Order Statuses

| Status | What it means |
|--------|---------------|
| Processing | Payment received — ready to pack and ship |
| Completed | You have shipped the order |
| Cancelled | The order was cancelled |
| Refunded | The customer was refunded |

### Checking Sales

Go to **WooCommerce > Analytics** to see:

- Total revenue
- Number of orders
- Best-selling products
- Customer information

### Stock

When you add products, check **Manage stock** and enter how many you have. WooCommerce will:

- Reduce stock automatically when someone buys
- Email you when stock gets low (below 3)
- Hide sold-out products from the shop

---

## Good to Know

### Currency Switcher

Your site has a currency switcher in the header. Customers can browse prices in ISK, USD, EUR, GBP, NOK, DKK, or SEK. This is just for browsing — the actual payment is always in ISK.

### Free Worldwide Shipping

Your store offers free worldwide shipping. This is shown to customers on the checkout page.

### Discount Codes

To create a discount code:

1. Go to **Marketing > Coupons**
2. Click **Add coupon**
3. Set the code (e.g., WELCOME10), discount type (% or fixed), and amount
4. Optionally set an expiry date or minimum order amount
5. Share the code with customers — they enter it at checkout

### Email Notifications

Customers automatically receive emails when:

- Their order is confirmed (payment received)
- Their order is marked as completed (shipped)

These emails are already branded with your Svaeria styling.

---

## Need Help?

For anything technical — theme changes, website issues, or questions about settings — contact Kjartan.
