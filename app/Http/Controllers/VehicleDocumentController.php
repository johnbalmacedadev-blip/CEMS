<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleDocument;
use App\Models\DocumentFormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleDocumentController extends Controller
{
    /**
     * Display all documents for a vehicle.
     */
    public function index(Vehicle $vehicle)
    {
        $vehicle->load([
            'acquisitionDocuments.files',
            'reservationDocuments.files',
            'releaseDocuments.files'
        ]);

        // Get all documents grouped by process type
        $acquisitionDocuments = $vehicle->acquisitionDocuments;
        $reservationDocuments = $vehicle->reservationDocuments;
        $releaseDocuments = $vehicle->releaseDocuments;

        return view('vehicles.documents.index', compact('vehicle', 'acquisitionDocuments', 'reservationDocuments', 'releaseDocuments'));
    }

    /**
     * Show the blank add details page.
     */
    public function addDetails(Vehicle $vehicle, $documentType, Request $request)
    {
        // Get process type from request (default to ACQUISITION)
        $processType = $request->get('process_type', 'ACQUISITION');
        
        // Validate document type based on process type
        if ($processType === 'RESERVATION') {
            $validTypes = [
                'IDS', 'AR', 'SOURCE_OF_SALE', 'POSTING_OF_GRAPHICS'
            ];
        } elseif ($processType === 'RELEASE') {
            $validTypes = [
                'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE', 'CONSENT_FORM'
            ];
        } else {
            $validTypes = [
                'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE',
                'VOLUNTARY_SURRENDER', 'SHERRIF_LETTER', 'DEED_OF_SALE_BANK'
            ];
        }
        
        if (!in_array($documentType, $validTypes)) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Invalid document type.');
        }

        // Get existing document if any
        $document = VehicleDocument::with('files')
            ->where('vehicle_id', $vehicle->id)
            ->where('document_type', $documentType)
            ->where('process_type', $processType)
            ->first();

        return view('vehicles.documents.add-details', compact('vehicle', 'documentType', 'processType', 'document'));
    }

    /**
     * Show the form for creating/editing a document.
     */
    public function create(Vehicle $vehicle, $documentType, Request $request)
    {
        // Get process type from request (default to ACQUISITION)
        $processType = $request->get('process_type', 'ACQUISITION');
        
        // Validate document type based on process type
        if ($processType === 'RESERVATION') {
            $validTypes = [
                'IDS', 'AR', 'SOURCE_OF_SALE', 'POSTING_OF_GRAPHICS'
            ];
        } elseif ($processType === 'RELEASE') {
            $validTypes = [
                'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE', 'CONSENT_FORM'
            ];
        } else {
            $validTypes = [
                'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE',
                'VOLUNTARY_SURRENDER', 'SHERRIF_LETTER', 'DEED_OF_SALE_BANK'
            ];
        }
        
        if (!in_array($documentType, $validTypes)) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Invalid document type.');
        }

        // Get existing document if any
        $document = VehicleDocument::with('files')
            ->where('vehicle_id', $vehicle->id)
            ->where('document_type', $documentType)
            ->where('process_type', $processType)
            ->first();

        // Get available templates for this document type
        $templates = DocumentFormTemplate::active()
            ->forDocumentType($documentType)
            ->orderBy('name')
            ->get();

        return view('vehicles.documents.create', compact('vehicle', 'documentType', 'document', 'templates', 'processType'));
    }

    /**
     * Store a newly created document.
     */
    public function store(Request $request, Vehicle $vehicle, $documentType)
    {
        // Get process type from request (default to ACQUISITION)
        $processType = $request->get('process_type', 'ACQUISITION');
        
        // Validate document type based on process type
        if ($processType === 'RESERVATION') {
            $validTypes = [
                'IDS', 'AR', 'SOURCE_OF_SALE', 'POSTING_OF_GRAPHICS'
            ];
        } else {
            $validTypes = [
                'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE',
                'VOLUNTARY_SURRENDER', 'SHERRIF_LETTER', 'DEED_OF_SALE_BANK'
            ];
        }
        
        if (!in_array($documentType, $validTypes)) {
            return redirect()->route('vehicles.show', $vehicle)
                ->with('error', 'Invalid document type.');
        }

        $request->validate([
            'storage_type' => 'nullable|in:file,link,form',
            'file' => 'nullable|file|max:10240', // Single file (backward compatibility)
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // Multiple files
            'file_link' => 'nullable|url', // Single link (backward compatibility)
            'file_links' => 'nullable|array',
            'file_links.*' => 'url', // Multiple links
            'form_data' => 'required_if:storage_type,form|nullable',
            'notes' => 'nullable|string',
            'is_completed' => 'nullable|boolean',
            'check_date' => 'nullable|date',
            'checked_by' => 'nullable|string|max:255',
        ]);

        // Check if document already exists
        $document = VehicleDocument::where('vehicle_id', $vehicle->id)
            ->where('document_type', $documentType)
            ->where('process_type', $processType)
            ->first();

        // If no storage type is provided, default to 'form' with empty data
        $storageType = $request->storage_type ?? 'form';

        $data = [
            'vehicle_id' => $vehicle->id,
            'document_type' => $documentType,
            'process_type' => $processType,
            'storage_type' => $storageType,
            'notes' => $request->notes,
            'is_completed' => $request->has('is_completed') ? (bool)$request->is_completed : ($document ? $document->is_completed : false),
            'check_date' => $request->check_date,
            'checked_by' => $request->checked_by,
        ];

        // Handle form data
        if ($storageType === 'form') {
            // Handle form_data - could be JSON string or array
            $formData = $request->form_data;
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
            }
            $data['form_data'] = $formData ?: [];
            
            // Store form_fields structure if provided
            $formFields = $request->form_fields;
            if (is_string($formFields)) {
                $formFields = json_decode($formFields, true);
            }
            // Store form_fields in form_data as metadata
            if ($formFields) {
                $data['form_data']['_form_structure'] = $formFields;
            }
            
            $data['file_path'] = null;
            $data['file_name'] = null;
            $data['file_link'] = null;
        } else {
            // For file/link storage, clear form_data
            $data['form_data'] = null;
        }

        if ($document) {
            $document->update($data);
        } else {
            // If no storage type data provided, set default empty form data
            if (!$request->has('storage_type') || empty($request->storage_type)) {
                $data['form_data'] = [];
            }
            $document = VehicleDocument::create($data);
        }

        // Handle multiple files
        if ($request->storage_type === 'file') {
            // Handle multiple files
            if ($request->hasFile('files')) {
                $sortOrder = $document->files()->max('sort_order') ?? 0;
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . uniqid() . '_' . Str::slug($file->getClientOriginalName());
                    $filePath = $file->storeAs('vehicles/documents', $fileName, 'public');
                    
                    \App\Models\VehicleDocumentFile::create([
                        'vehicle_document_id' => $document->id,
                        'type' => 'file',
                        'file_path' => $filePath,
                        'file_name' => $file->getClientOriginalName(),
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            }
            // Handle single file (backward compatibility)
            elseif ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . uniqid() . '_' . Str::slug($file->getClientOriginalName());
                $filePath = $file->storeAs('vehicles/documents', $fileName, 'public');
                
                \App\Models\VehicleDocumentFile::create([
                    'vehicle_document_id' => $document->id,
                    'type' => 'file',
                    'file_path' => $filePath,
                    'file_name' => $file->getClientOriginalName(),
                    'sort_order' => 1,
                ]);
            }
        }
        
        // Handle multiple links
        if ($request->storage_type === 'link') {
            // Handle multiple links
            if ($request->has('file_links') && is_array($request->file_links)) {
                $sortOrder = $document->files()->max('sort_order') ?? 0;
                foreach ($request->file_links as $link) {
                    if (!empty(trim($link))) {
                        \App\Models\VehicleDocumentFile::create([
                            'vehicle_document_id' => $document->id,
                            'type' => 'link',
                            'file_link' => trim($link),
                            'sort_order' => ++$sortOrder,
                        ]);
                    }
                }
            }
            // Handle single link (backward compatibility)
            elseif ($request->has('file_link') && !empty($request->file_link)) {
                \App\Models\VehicleDocumentFile::create([
                    'vehicle_document_id' => $document->id,
                    'type' => 'link',
                    'file_link' => $request->file_link,
                    'sort_order' => 1,
                ]);
            }
        }

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Document saved successfully!');
    }

    /**
     * Display the specified document.
     */
    public function show(Vehicle $vehicle, VehicleDocument $document)
    {
        // Load files relationship
        $document->load('files');
        
        return view('vehicles.documents.show', compact('vehicle', 'document'));
    }

    /**
     * Delete a document.
     */
    public function destroy(Vehicle $vehicle, VehicleDocument $document)
    {
        // Delete file if exists
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * Download a document file.
     */
    public function download(Vehicle $vehicle, VehicleDocument $document)
    {
        // Check for files in the new structure first
        $file = $document->files()->where('type', 'file')->first();
        if ($file) {
            return Storage::disk('public')->download($file->file_path, $file->file_name);
        }
        
        // Fallback to old structure for backward compatibility
        if ($document->file_path) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        return redirect()->back()->with('error', 'No file available for download.');
    }

    /**
     * Download a specific file from a document.
     */
    public function downloadFile(Vehicle $vehicle, $fileId)
    {
        $file = \App\Models\VehicleDocumentFile::findOrFail($fileId);
        
        // Verify the file belongs to a document for this vehicle
        if ($file->vehicleDocument->vehicle_id !== $vehicle->id) {
            abort(403, 'Unauthorized access to this file.');
        }

        if ($file->type !== 'file' || !$file->file_path) {
            return redirect()->back()->with('error', 'No file available for download.');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }

    /**
     * Mark document as completed
     */
    public function markCompleted(Vehicle $vehicle, VehicleDocument $document)
    {
        $document->update(['is_completed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Document marked as completed!',
            'document_id' => $document->id
        ]);
    }

    /**
     * Mark new document as completed (when document doesn't exist yet)
     */
    public function markNewCompleted(Request $request, Vehicle $vehicle)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');
        
        // Log the incoming request for debugging
        \Log::info('markNewCompleted called', [
            'vehicle_id' => $vehicle->id,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path()
        ]);
        
        try {
            // Get data from request body (JSON) or form data
            $jsonData = $request->json()->all();
            $documentType = $jsonData['document_type'] ?? $request->input('document_type');
            $processType = $jsonData['process_type'] ?? $request->input('process_type', 'ACQUISITION');
            
            \Log::info('Mark new completed request:', [
                'vehicle_id' => $vehicle->id,
                'document_type' => $documentType,
                'process_type' => $processType,
                'all_input' => $request->all(),
                'json_data' => $jsonData,
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);
            
            if (!$documentType) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document type is required'
                ], 400);
            }
            
            // Validate document type based on process type
            if ($processType === 'RESERVATION') {
                $validTypes = ['IDS', 'AR', 'SOURCE_OF_SALE', 'POSTING_OF_GRAPHICS'];
            } elseif ($processType === 'RELEASE') {
                $validTypes = [
                    'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                    'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE', 'CONSENT_FORM'
                ];
            } else {
                $validTypes = [
                    'OR', 'CR', 'AR', 'IDS', 'PROMISSORY', 'CHATTEL',
                    'REGISTRY_OF_DEEDS', 'SEC_CERT', 'DEED_OF_SALE',
                    'VOLUNTARY_SURRENDER', 'SHERRIF_LETTER', 'DEED_OF_SALE_BANK'
                ];
            }
            
            if (!in_array($documentType, $validTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid document type for this process type'
                ], 400);
            }
            
            $document = VehicleDocument::updateOrCreate(
                [
                    'vehicle_id' => $vehicle->id,
                    'document_type' => $documentType,
                    'process_type' => $processType,
                ],
                [
                    'storage_type' => 'form',
                    'form_data' => [],
                    'is_completed' => true,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Document marked as completed!',
                'document_id' => $document->id
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking document as completed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark document as incomplete
     */
    public function markIncomplete(Vehicle $vehicle, VehicleDocument $document)
    {
        $document->update(['is_completed' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Document marked as incomplete!'
        ]);
    }

    /**
     * Get form template
     */
    public function getTemplate(DocumentFormTemplate $template)
    {
        return response()->json([
            'success' => true,
            'template' => $template
        ]);
    }

    /**
     * Save form template
     */
    public function saveTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'document_type' => 'required|string',
            'form_fields' => 'required|array',
            'form_fields.*.label' => 'required|string',
            'form_fields.*.type' => 'required|string|in:text,number,date,textarea,select,checkbox,radio',
            'form_fields.*.name' => 'required|string',
            'form_fields.*.options' => 'nullable|array', // For select/radio
            'form_fields.*.required' => 'nullable|boolean',
            'form_fields.*.placeholder' => 'nullable|string',
        ]);

        $template = DocumentFormTemplate::create([
            'name' => $request->name,
            'document_type' => $request->document_type,
            'form_fields' => $request->form_fields,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Template saved successfully!',
            'template' => $template
        ]);
    }

    /**
     * Show the form for editing a document.
     */
    public function edit(Vehicle $vehicle, VehicleDocument $document)
    {
        // Load document with relationships
        $document->load('files');
        
        // Get available templates for this document type
        $templates = DocumentFormTemplate::active()
            ->forDocumentType($document->document_type)
            ->orderBy('name')
            ->get();

        return view('vehicles.documents.edit', compact('vehicle', 'document', 'templates'));
    }

    /**
     * Update a document.
     */
    public function update(Request $request, Vehicle $vehicle, VehicleDocument $document)
    {
        $request->validate([
            'storage_type' => 'nullable|in:file,link,form',
            'file' => 'nullable|file|max:10240',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240',
            'file_link' => 'nullable|url',
            'file_links' => 'nullable|array',
            'file_links.*' => 'url',
            'form_data' => 'required_if:storage_type,form|nullable',
            'notes' => 'nullable|string',
            'is_completed' => 'nullable|boolean',
            'check_date' => 'nullable|date',
            'checked_by' => 'nullable|string|max:255',
        ]);

        $storageType = $request->storage_type ?? $document->storage_type ?? 'form';

        $data = [
            'storage_type' => $storageType,
            'notes' => $request->notes,
            'is_completed' => $request->has('is_completed') ? (bool)$request->is_completed : $document->is_completed,
            'check_date' => $request->check_date,
            'checked_by' => $request->checked_by,
        ];

        // Handle form data
        if ($storageType === 'form') {
            $formData = $request->form_data;
            if (is_string($formData)) {
                $formData = json_decode($formData, true);
            }
            $data['form_data'] = $formData ?: [];
        } else {
            // Clear form_data if storage type is not form
            $data['form_data'] = null;
        }

        // Handle file links - delete existing and create new ones
        if ($storageType === 'link') {
            // Delete existing link files
            $document->files()->where('type', 'link')->delete();
            
            // Create new link files
            if ($request->has('file_links') && is_array($request->file_links)) {
                $sortOrder = $document->files()->max('sort_order') ?? 0;
                foreach ($request->file_links as $link) {
                    if (!empty(trim($link))) {
                        \App\Models\VehicleDocumentFile::create([
                            'vehicle_document_id' => $document->id,
                            'type' => 'link',
                            'file_link' => trim($link),
                            'file_name' => basename(parse_url(trim($link), PHP_URL_PATH)) ?: 'Link',
                            'sort_order' => ++$sortOrder,
                        ]);
                    }
                }
            }
        } else {
            // If storage type is not link, delete all link files
            $document->files()->where('type', 'link')->delete();
        }

        // Update document
        $document->update($data);

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Document updated successfully.');
    }
}
