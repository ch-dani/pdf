<template>
    <div data-options='{"touch": false, "autoDimensions": false}' class="pop-up" id="redo-modal" style="display: none; width: 450px;">
        <div class="modal-header">
            <div class="modal-title">Redo changes</div>
        </div>
        <div class="modal-body">
            <div class="alert-info">No changes found</div>
            <div class="undo-table-wrap">
                <table id="undo_table" style=" width: 100%; ">
                    <thead>
                    </thead>

                    <tbody>
                    <tr class="example_tr hidden" undo-element-id="%element_id%">
                        <td class="check" style="cursor: pointer;">
                            <label>
                                <input class="revert_item" type="checkbox" value="%element_id%" element-type="%element_type%">
                            </label>
                        </td>
                        <td class="what" style="cursor: pointer;" valign="baseline"><i class="fas fa-%element_type%"></i><span class='undo_text' title="">%action%</span></td>
                        <td class="when" style="cursor: pointer;">%time%</td>
                        <td class="where" style="cursor: pointer;">%page%</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-find-next " id="time_to_redo" type="button">Redo selected</button>
        </div>
    </div>
</template>