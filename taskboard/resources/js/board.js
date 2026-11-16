import Sortable from 'sortablejs';

const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

// Keep the little number badge on each column correct after a move.
function updateCounts() {
    document.querySelectorAll('[data-task-list]').forEach((list) => {
        const column = list.closest('[data-column]');
        const badge = column?.querySelector('[data-count]');
        if (badge) {
            badge.textContent = list.querySelectorAll('[data-task-id]').length;
        }
    });
}

document.querySelectorAll('[data-task-list]').forEach((list) => {
    Sortable.create(list, {
        group: 'tasks',
        draggable: '[data-task-id]',
        animation: 150,

        forceFallback: true,        // smooth mouse-based drag instead of the browser's link drag
        fallbackTolerance: 3,       // small movement still counts as a click, not a drag
        emptyInsertThreshold: 30,   // easier to drop into empty or short columns
        scroll: true,
        scrollSensitivity: 80,

        onEnd: (evt) => {
            // Dropped back in the same spot: nothing to save.
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