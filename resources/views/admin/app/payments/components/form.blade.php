<div class="box">
    <div class="box-body">
        <div class="input">
            <label>
                <span class="label">ID</span>
                <input type="text" value="{{ $transaction->id }}" disabled>
            </label>
        </div>

        <div class="input">
            <label>
                <span class="label">Пользователь</span>
                <input type="text" value="{{ $transaction->user?->login ?? $transaction->user?->email ?? '—' }}" disabled>
            </label>
        </div>

        <div class="input">
            <label>
                <span class="label">Сумма</span>
                <input type="text" value="${{ number_format((float)$transaction->amount_usd, 2, '.', '') }}" disabled>
            </label>
        </div>

        <div class="input">
            <label>
                <span class="label">Метод</span>
                <input type="text" value="{{ strtoupper($transaction->method) }}" disabled>
            </label>
        </div>

        <div class="input">
            <label>
                <span class="label">Статус</span>
                <div class="input-content">
                    @if($transaction->status === 'completed')
                        <span class="d-label label-success">Completed</span>
                    @elseif($transaction->status === 'pending')
                        <span class="d-label label-warning">Pending</span>
                    @else
                        <span class="d-label label-danger">Failed</span>
                    @endif
                </div>
            </label>
        </div>

        <div class="input">
            <label>
                <span class="label">Дата создания</span>
                <input type="text" value="{{ $transaction->created_at }}" disabled>
            </label>
        </div>

        @if(isset($transaction->meta['receipt_path']))
            <div class="input">
                <label>
                    <span class="label">Чек</span>
                    <div class="input-content">
                        @php $mime = $transaction->meta['receipt_mime'] ?? null; @endphp
                        @if($mime && str_starts_with($mime, 'image/'))
                            <div class="mb-2">
                                <a href="{{ $transaction->meta['receipt_path'] }}" target="_blank">
                                    <img src="{{ $transaction->meta['receipt_path'] }}" alt="receipt">
                                </a>
                            </div>
                        @endif
                    </div>
                </label>
            </div>
        @endif
    </div>
</div>
