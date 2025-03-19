<?php

namespace App\Telegram\Enums;

enum CustomerCommandEnum:string
{
  case START = "/start";
  case ORDERS = "📦 Buyurtmalarim";
  case BALANCE = "💵 Balans";
  case DOCS = "📄 Aktsverka";
  case NEW_ORDERS = "🆕 Yangi buyurtma";
}