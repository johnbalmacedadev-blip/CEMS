<?php

namespace App\Http\Controllers;

use App\Models\DocumentFormTemplate;
use Illuminate\Http\Request;

class DocumentFormTemplateController extends Controller
{
    /**
     * Display a listing of form templates.
     */
    public function index()
    {
        $templates = DocumentFormTemplate::orderBy('document_type')
            ->orderBy('name')
            ->get();

        return view('document-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $documentTypes = [
            'OR' => 'OR',
            'CR' => 'CR',
            'AR' => 'AR',
            'IDS' => 'IDS',
            'PROMISSORY' => 'PROMISSORY',
            'CHATTEL' => 'CHATTEL',
            'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
            'SEC_CERT' => 'SEC CERT',
            'DEED_OF_SALE' => 'DEED OF SALE',
            'VOLUNTARY_SURRENDER' => 'VOLUNTARY SURRENDER',
            'SHERRIF_LETTER' => 'SHERRIF LETTER',
            'DEED_OF_SALE_BANK' => 'DEED OF SALE (BANK)',
            'SOURCE_OF_SALE' => 'SOURCE OF SALE',
            'POSTING_OF_GRAPHICS' => 'POSTING OF GRAPHICS',
            'CONSENT_FORM' => 'CONSENT FORM',
        ];

        return view('document-templates.create', compact('documentTypes'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string',
            'form_fields' => 'required|array|min:1',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|string|in:text,number,date,textarea,select,checkbox,radio',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.required' => 'nullable|boolean',
            'form_fields.*.placeholder' => 'nullable|string',
        ]);

        // Process form fields and handle options
        $formFields = [];
        foreach ($request->form_fields as $field) {
            $processedField = [
                'label' => $field['label'],
                'type' => $field['type'],
                'name' => $field['name'],
                'required' => isset($field['required']) ? true : false,
                'placeholder' => $field['placeholder'] ?? null,
            ];
            
            // Handle options for select/radio fields
            if (in_array($field['type'], ['select', 'radio']) && !empty($field['options'])) {
                if (is_string($field['options'])) {
                    // Convert comma-separated string to array
                    $processedField['options'] = array_map('trim', explode(',', $field['options']));
                } else {
                    $processedField['options'] = $field['options'];
                }
            }
            
            $formFields[] = $processedField;
        }

        DocumentFormTemplate::create([
            'name' => $request->name,
            'document_type' => $request->document_type,
            'form_fields' => $formFields,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('document-templates.index')
            ->with('success', 'Form template created successfully!');
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(DocumentFormTemplate $template)
    {
        $documentTypes = [
            'OR' => 'OR',
            'CR' => 'CR',
            'AR' => 'AR',
            'IDS' => 'IDS',
            'PROMISSORY' => 'PROMISSORY',
            'CHATTEL' => 'CHATTEL',
            'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
            'SEC_CERT' => 'SEC CERT',
            'DEED_OF_SALE' => 'DEED OF SALE',
            'VOLUNTARY_SURRENDER' => 'VOLUNTARY SURRENDER',
            'SHERRIF_LETTER' => 'SHERRIF LETTER',
            'DEED_OF_SALE_BANK' => 'DEED OF SALE (BANK)',
            'SOURCE_OF_SALE' => 'SOURCE OF SALE',
            'POSTING_OF_GRAPHICS' => 'POSTING OF GRAPHICS',
            'CONSENT_FORM' => 'CONSENT FORM',
        ];

        return view('document-templates.edit', compact('template', 'documentTypes'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, DocumentFormTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string',
            'form_fields' => 'required|array|min:1',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|string|in:text,number,date,textarea,select,checkbox,radio',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.required' => 'nullable|boolean',
            'form_fields.*.placeholder' => 'nullable|string',
        ]);

        // Process form fields and handle options
        $formFields = [];
        foreach ($request->form_fields as $field) {
            $processedField = [
                'label' => $field['label'],
                'type' => $field['type'],
                'name' => $field['name'],
                'required' => isset($field['required']) ? true : false,
                'placeholder' => $field['placeholder'] ?? null,
            ];
            
            // Handle options for select/radio fields
            if (in_array($field['type'], ['select', 'radio']) && !empty($field['options'])) {
                if (is_string($field['options'])) {
                    // Convert comma-separated string to array
                    $processedField['options'] = array_map('trim', explode(',', $field['options']));
                } else {
                    $processedField['options'] = $field['options'];
                }
            }
            
            $formFields[] = $processedField;
        }

        $template->update([
            'name' => $request->name,
            'document_type' => $request->document_type,
            'form_fields' => $formFields,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('document-templates.index')
            ->with('success', 'Form template updated successfully!');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(DocumentFormTemplate $template)
    {
        $template->delete();

        return redirect()->route('document-templates.index')
            ->with('success', 'Form template deleted successfully!');
    }
}
