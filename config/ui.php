<?php

return [
    // --- Layout & Container ---
    'card_container' => 'overflow-x-auto glass-panel',
    'card_header'    => 'px-6 py-4 glass-header flex justify-between items-center rounded-t-xl',
    'card_title'     => 'text-lg font-semibold text-glass-title',

    // --- Buttons ---
    'btn_primary'    => 'bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center gap-2',
    'btn_secondary'  => 'px-5 py-2.5 text-indigo-200 bg-indigo-900/40 hover:bg-indigo-800/50 rounded-lg font-semibold transition-colors border border-indigo-700/50',
    'btn_submit'     => 'px-5 py-2.5 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg font-semibold shadow-sm transition-colors flex items-center gap-2',
    
    // Action Buttons (Table)
    'btn_action_wrap'=> 'flex justify-center gap-2',
    'btn_edit'       => 'px-4 py-2 bg-amber-500 text-white text-xs font-bold rounded-lg hover:bg-amber-600 transition-colors uppercase shadow-sm',
    'btn_delete'     => 'px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-lg hover:bg-red-700 transition-colors uppercase shadow-sm',
    'btn_reset'      => 'px-3 py-1.5 bg-amber-900/40 text-amber-300 hover:bg-amber-800/50 border border-amber-600/50 rounded-lg font-medium text-xs transition-colors',
    'btn_delete_sm'  => 'px-3 py-1.5 bg-red-900/40 text-red-300 hover:bg-red-800/50 border border-red-600/50 rounded-lg font-medium text-xs transition-colors shadow-sm',

    // --- Table ---
    'table_main'     => 'w-full text-sm text-left glass-table text-glass-subtitle',
    'table_head'     => 'uppercase font-semibold text-xs',
    'table_th'       => 'px-6 py-4',
    'table_th_center'=> 'px-6 py-4 text-center',
    'table_row'      => 'transition-colors',
    'table_td'       => 'px-6 py-4',
    
    // --- Alert Messages ---
    'alert_success'  => 'bg-emerald-900/40 border border-emerald-500/50 text-emerald-300 px-4 py-3 rounded mb-4 shadow-sm backdrop-blur-sm',
    'alert_error'    => 'bg-red-900/40 border border-red-500/50 text-red-300 px-4 py-3 rounded mb-4 shadow-sm backdrop-blur-sm',

    // --- Modal ---
    'modal_overlay'  => 'fixed inset-0 bg-black/80 z-[100] hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300 backdrop-blur-sm',
    'modal_content'  => 'glass-panel w-full shadow-2xl transform scale-90 opacity-0 -translate-y-10 transition-all duration-300',
    'modal_header'   => 'p-6 glass-header flex justify-between items-center rounded-t-xl',
    'modal_title'    => 'text-xl font-bold text-glass-title',
    'modal_close'    => 'text-indigo-300 hover:text-indigo-100 transition-colors',
    'modal_body'     => 'p-6 text-glass-subtitle',
    'modal_footer'   => 'mt-8 flex justify-end gap-3 border-t border-indigo-900/50 pt-5',
    
    // --- Forms ---
    'form_group'     => 'flex flex-col gap-1.5',
    'form_label'     => 'text-sm font-semibold text-glass-subtitle',
    'form_input'     => 'w-full px-4 py-2.5 rounded-lg glass-input-ui outline-none transition-all',
    'form_grid'      => 'grid grid-cols-1 gap-5',
    'form_grid_2'    => 'grid grid-cols-1 md:grid-cols-2 gap-5',

    // --- Dashboard Specific ---
    'stat_card_label'=> 'text-xs text-indigo-300 uppercase font-semibold tracking-wide',
    'stat_card_value'=> 'text-3xl font-bold text-glass-title',
    'stat_card_desc' => 'text-xs text-indigo-400',
    
    'dashboard_table'=> 'w-full text-sm text-glass-subtitle glass-table',
    'dashboard_thead'=> 'text-xs uppercase text-indigo-300 font-semibold',
    'dashboard_th'   => 'px-4 py-3 text-left',
    'dashboard_tr'   => 'transition-colors',
    'dashboard_td'   => 'px-4 py-3',
];
