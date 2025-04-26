Movie, TV Show, and Anime Review Platform
A full-stack web application for discovering, reviewing, and chatting about movies, TV shows, and anime — complete with a real-time chat, secure downloads, and admin management tools.

📌 Table of Contents
Overview

Objectives

Features

User Roles & Permissions

Tech Stack

Database Schema

Design & UX

Setup Instructions

Project Phases & Timeline

Security & Performance

Deliverables

🔍 Overview
This project provides a rich, interactive platform for users to:

Browse, search, and filter a wide range of movies, TV shows, and anime.

Leave ratings and comments.

Join real-time chat rooms (1-on-1 and groups).

Securely download content (for premium users).

Administer and moderate the platform via a dedicated dashboard.

🎯 Objectives
Enable users to discover, review, and download media content.

Foster an engaging community experience through comments, ratings, and real-time chat.

Provide administrators with tools for managing content, users, and conversations.

💡 Features
✅ Core Functionalities
Authentication & Authorization

Email/password login

OAuth with Google

Password reset tokens

Role-based access

Media Catalog

View poster, description, genres, rating, trailer, cast

Related titles recommendations

Ratings & Comments

0–10 scoring system

Comment threads with like/dislike

Admin moderation

Favorites & Watchlists

Premium users save favorites and manage personal watchlists

Downloads

Secure, expiring links for premium members

Real-Time Chat

1-on-1 messages

Group chats with profile pictures, image attachments, and timestamps

React + WebSocket interface

Admin Panel

Media CRUD operations

User ban/unban

Category management

Chat moderation

👤 User Roles & Permissions

Role	Capabilities
Guest	Browse, search, view media details
User	All guest permissions + comment and rate
Premium	All user permissions + favorites, downloads, chat access
Admin	All premium permissions + content moderation, media/user CRUD, category management

🧱 Tech Stack
Frontend: HTML5, CSS3, JavaScript

Backend API: Laravel (PHP)

Real-Time Chat: Node.js + Express.js + WebSocket (ws)

Database: MySQL

Authentication: JWT, OAuth

Payments: Stripe API

Version Control: Git, GitHub

🗃️ Database Schema (Key Tables)

Table	Purpose
users	User info (name, email, profilePic)
movies, tv_shows	Media content metadata
categories	Media genres
categoryables	Polymorphic genre mapping
comments	User reviews and ratings
subscriptions	Stripe-based premium tracking
user_movie_list	Watchlist management
messages	1-on-1 chat
groups, group_messages	Group chat system

🎨 Design & UX
User Interface
Homepage: Trending carousel, search bar, filters

Detail Page: Media info, comments, related titles

Chat Widget: React UI with user list, real-time input

Admin Panel: Sidebar nav, CRUD forms, tables

⚙️ Setup Instructions
Backend (Laravel API)

git clone https://github.com/aabid-io-khalid/Red-File
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
Frontend (React Chat & UI)
 

git clone https://github.com/aabid-io-khalid/Red-File
cd chat-client
npm install
npm start
WebSocket Server


git clone https://github.com/aabid-io-khalid/Red-File
cd chat-server
npm install
node server.js




