import Sortable from 'sortablejs';

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
const board = document.querySelector('[data-board]');

// Keep the little number badge on each column correct.
function updateCounts() {
    document.querySelectorAll('[data-task-list]').forEach((list) => {
        const column = list.closest('[data-column]');
        const badge = column?.querySelector('[data-count]');
        if (badge) {
            badge.textContent = list.querySelectorAll('[data-task-id]').length;
        }
    });
}

// Put the cards of a column in the given order.
function applyOrder(list, ids) {
    ids.forEach((id) => {
        const card = list.querySelector(`[data-task-id="${id}"]`);
        if (card) list.appendChild(card);
    });
}

/* ---------- Drag and drop ---------- */
document.querySelectorAll('[data-task-list]').forEach((list) => {
    Sortable.create(list, {
        group: 'tasks',
        draggable: '[data-task-id]',
        animation: 150,

        forceFallback: true,
        fallbackTolerance: 3,
        emptyInsertThreshold: 30,
        scroll: true,
        scrollSensitivity: 80,

        onEnd: (evt) => {
            if (evt.from === evt.to && evt.oldIndex === evt.newIndex) {
                return;
            }

            const taskId = evt.item.dataset.taskId;
            const columnId = evt.to.dataset.columnId;
            const orderedIds = [...evt.to.querySelectorAll('[data-task-id]')]
                .map((el) => Number(el.dataset.taskId));

            updateCounts();

            fetch(`/tasks/${taskId}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    board_column_id: columnId,
                    ordered_ids: orderedIds,
                }),
            })
                .then((response) => {
                    if (!response.ok) throw new Error('Move failed');
                })
                .catch(() => {
                    alert('Could not save that move. The board will reload.');
                    window.location.reload();
                });
        },
    });
});

/* ---------- Live updates from other people ---------- */
if (board && window.Echo) {
    window.Echo.private(`project.${board.dataset.projectId}`)
        .listen('.board.updated', (e) => {
            const existing = document.querySelector(`[data-task-id="${e.taskId}"]`);

            if (e.type === 'deleted') {
                existing?.remove();
                updateCounts();
                return;
            }

            const list = document.querySelector(
                `[data-task-list][data-column-id="${e.columnId}"]`
            );
            if (!list) return;

            let card = existing;

            // Use the fresh card HTML sent by the server.
            if (e.html) {
                const template = document.createElement('template');
                template.innerHTML = e.html.trim();
                const fresh = template.content.firstElementChild;

                if (card) card.replaceWith(fresh);
                card = fresh;
            }

            if (card && card.parentElement !== list) {
                list.appendChild(card);
            }

            applyOrder(list, e.orderedIds);
            updateCounts();
        });
}