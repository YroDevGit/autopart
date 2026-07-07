<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History · Products + Ratings</title>
  <!-- Font Awesome 6 (Free) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    body {
      background: #f4f6fa;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 2rem 1rem;
    }

    .card {
      max-width: 1100px;
      width: 100%;
      background: white;
      border-radius: 32px;
      box-shadow: 0 20px 40px -12px rgba(0, 20, 30, 0.25);
      padding: 2rem 2rem 2.5rem;
      transition: 0.2s;
    }

    h1 {
      font-size: 1.9rem;
      font-weight: 600;
      letter-spacing: -0.02em;
      color: #0b1a2b;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
      border-bottom: 2px solid #eef2f6;
      padding-bottom: 1.2rem;
    }

    h1 i {
      color: #2563eb;
      font-size: 1.7rem;
    }

    .order-list {
      display: flex;
      flex-direction: column;
      gap: 0.9rem;
    }

    .order-row {
      background: #f9fafc;
      border-radius: 18px;
      padding: 1rem 1.5rem;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      border: 1px solid #e9edf2;
      transition: all 0.15s;
      cursor: pointer;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .order-row:hover {
      background: #ffffff;
      border-color: #cbd5e1;
      box-shadow: 0 6px 14px rgba(0, 20, 30, 0.06);
      transform: translateY(-1px);
    }

    .order-info {
      display: flex;
      flex-wrap: wrap;
      align-items: baseline;
      gap: 0.75rem 1.5rem;
    }

    .order-id {
      font-weight: 600;
      color: #0f2a44;
      letter-spacing: -0.01em;
      font-size: 1.05rem;
    }

    .order-date {
      color: #4e5f71;
      font-size: 0.9rem;
      background: #eef2f6;
      padding: 0.2rem 0.8rem;
      border-radius: 30px;
      font-weight: 450;
    }

    .order-total {
      font-weight: 600;
      color: #1b2f44;
      background: #e6edf5;
      padding: 0.2rem 0.9rem;
      border-radius: 30px;
      font-size: 0.9rem;
    }

    .status-badge {
      font-size: 0.75rem;
      font-weight: 600;
      padding: 0.35rem 1rem;
      border-radius: 40px;
      background: #dbeafe;
      color: #1a4a8a;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .status-badge i {
      font-size: 0.65rem;
    }

    .status-badge.shipped {
      background: #d1fae5;
      color: #0b6e4f;
    }

    .status-badge.delivered {
      background: #d1fae5;
      color: #0b6e4f;
    }

    .status-badge.processing {
      background: #fef3c7;
      color: #a15813;
    }

    .status-badge.cancelled {
      background: #fee2e2;
      color: #991b1b;
    }

    .status-badge.pending {
      background: #e0f2fe;
      color: #1a4a8a;
    }

    .view-icon {
      color: #5f7d9c;
      font-size: 0.9rem;
      opacity: 0.7;
      transition: 0.2s;
    }

    .order-row:hover .view-icon {
      opacity: 1;
      color: #2563eb;
    }

    /* ----- MODAL ----- */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(10, 20, 35, 0.6);
      backdrop-filter: blur(4px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      visibility: hidden;
      opacity: 0;
      transition: all 0.2s ease;
      padding: 1.5rem;
    }

    .modal-overlay.active {
      visibility: visible;
      opacity: 1;
    }

    .modal-box {
      background: white;
      border-radius: 32px;
      max-width: 640px;
      width: 100%;
      max-height: 90vh;
      overflow-y: auto;
      padding: 2rem 2rem 1.8rem;
      box-shadow: 0 40px 60px -20px rgba(0, 0, 0, 0.4);
      transform: scale(0.96) translateY(8px);
      transition: all 0.2s ease;
    }

    .modal-overlay.active .modal-box {
      transform: scale(1) translateY(0);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      border-bottom: 1px solid #eef2f6;
      padding-bottom: 1rem;
    }

    .modal-header h2 {
      font-size: 1.4rem;
      font-weight: 600;
      color: #0b1a2b;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .modal-header h2 i {
      color: #2563eb;
    }

    .modal-close {
      background: #f1f4f9;
      border: none;
      width: 38px;
      height: 38px;
      border-radius: 40px;
      font-size: 1.2rem;
      color: #2b4058;
      cursor: pointer;
      transition: 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-close:hover {
      background: #e2e8f0;
      color: #0b1a2b;
    }

    .modal-body {
      display: flex;
      flex-direction: column;
      gap: 1.2rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px dashed #e6ecf3;
      padding-bottom: 0.6rem;
    }

    .detail-label {
      color: #4b627c;
      font-weight: 450;
      font-size: 0.95rem;
    }

    .detail-value {
      font-weight: 500;
      color: #0f2a44;
    }

    .detail-value.status {
      display: flex;
      align-items: center;
      gap: 0.4rem;
    }

    .modal-status-badge {
      font-size: 0.8rem;
      font-weight: 600;
      padding: 0.25rem 1rem;
      border-radius: 40px;
      text-transform: uppercase;
    }

    /* product list */
    .product-list {
      margin: 0.5rem 0 0.2rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .product-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      background: #f9fafc;
      padding: 0.7rem 1rem;
      border-radius: 16px;
      border: 1px solid #eef2f6;
    }

    .product-img {
      width: 52px;
      height: 52px;
      border-radius: 12px;
      object-fit: cover;
      background: #eef2f6;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      color: #7b8da0;
      flex-shrink: 0;
    }

    .product-details {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 0.2rem;
    }

    .product-name {
      font-weight: 550;
      color: #0b1a2b;
      font-size: 0.95rem;
    }

    .product-meta {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 0.8rem;
      font-size: 0.8rem;
      color: #4b627c;
    }

    .product-meta span {
      background: #eef2f6;
      padding: 0.1rem 0.6rem;
      border-radius: 30px;
    }

    .product-price {
      font-weight: 550;
      color: #0f2a44;
    }

    /* star rating (only for delivered) */
    .rating-stars {
      display: inline-flex;
      align-items: center;
      gap: 0.1rem;
      margin-left: 0.3rem;
    }

    .rating-stars i {
      color: #d1d9e6;
      font-size: 0.9rem;
      cursor: pointer;
      transition: 0.15s;
    }

    .rating-stars i.active {
      color: #fbbf24;
    }

    .rating-stars i:hover {
      transform: scale(1.15);
    }

    .rating-stars i.active:hover {
      color: #f59e0b;
    }

    .rating-label {
      font-size: 0.7rem;
      color: #7b8da0;
      margin-left: 0.2rem;
      font-weight: 450;
    }

    .modal-actions {
      margin-top: 1.2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: flex-end;
      gap: 0.8rem;
      border-top: 1px solid #eef2f6;
      padding-top: 1.5rem;
    }

    .btn {
      border: none;
      padding: 0.6rem 1.6rem;
      border-radius: 40px;
      font-weight: 550;
      font-size: 0.9rem;
      background: #f1f4f9;
      color: #1e2f40;
      cursor: pointer;
      transition: 0.15s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-primary {
      background: #2563eb;
      color: white;
    }

    .btn-primary:hover {
      background: #1d4ed8;
      transform: scale(0.97);
    }

    .btn-danger {
      background: #dc2626;
      color: white;
    }

    .btn-danger:hover {
      background: #b91c1c;
      transform: scale(0.97);
    }

    .btn-danger:disabled {
      opacity: 0.5;
      pointer-events: none;
      filter: grayscale(0.3);
    }

    .btn-secondary {
      background: #eef2f6;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .toast-message {
      margin-top: 0.8rem;
      padding: 0.5rem 1rem;
      background: #e6f7e6;
      color: #0b6e4f;
      border-radius: 40px;
      font-size: 0.85rem;
      font-weight: 500;
      text-align: center;
      border-left: 4px solid #22a06b;
      display: none;
    }

    .toast-message.show {
      display: block;
    }

    .empty-msg {
      text-align: center;
      color: #62748b;
      padding: 2.5rem 0;
    }

    .btnField {
      padding: 10px;
      margin-top: 10px;
    }

    .btnField a button {
      background: #1f314b;
      border: none;
      border-radius: 2rem;
      padding: 0.5rem 0;
      font-weight: 700;
      font-size: 0.9rem;
      color: #d1e0ff;
      border-bottom: 3px solid #0b1422;
      cursor: pointer;
      transition: 0.1s ease;
      box-shadow: 0 4px 0 #0a111f;

      padding-left: 10px;
      padding-right: 10px;
    }

    .btn-primary {
      background: #3b5b8a;
      color: white;
      border-bottom-color: #1b2d4a;
    }

    @media (max-width: 600px) {
      .card {
        padding: 1.2rem;
      }

      .order-row {
        flex-direction: column;
        align-items: stretch;
        gap: 0.6rem;
      }

      .order-info {
        gap: 0.4rem;
      }

      .modal-box {
        padding: 1.5rem;
      }

      .modal-actions {
        justify-content: center;
      }

      .product-item {
        flex-wrap: wrap;
      }
    }
  </style>
</head>

<body>

  <div class="card" id="app">
    <h1><i class="fas fa-clock-rotate-left"></i> Order history</h1>
    <div id="orderListContainer" class="order-list"></div>
    <div align='center' class="btnField">
      <a href="<?=prev_page?>"><button>Order now</button></a>
    </div>
  </div>

  <!-- MODAL -->
  <div class="modal-overlay" id="orderModal">
    <div class="modal-box">
      <div class="modal-header">
        <h2><i class="fas fa-receipt"></i> Order details</h2>
        <button class="modal-close" id="modalCloseBtn"><i class="fas fa-xmark"></i></button>
      </div>
      <div class="modal-body" id="modalBody"></div>
      <div class="modal-actions">
        <button class="btn btn-secondary" id="modalCloseBtn2"><i class="fas fa-arrow-left"></i> Back to orders</button>
        <button class="btn btn-danger" id="cancelOrderBtn"><i class="fas fa-ban"></i> Cancel order</button>
      </div>
      <div id="modalToast" class="toast-message">✅ Order cancelled</div>
    </div>
  </div>
</body>

</html>

<?=js()?>