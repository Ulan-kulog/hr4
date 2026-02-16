<?php
require_once __DIR__ . '/DB.php';
session_start();

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    handleAjaxRequest();
    exit;
}

// Fetch providers from database
$providers = Database::fetchAll("SELECT * FROM providers");
$totalProviders = count($providers);

// Calculate statistics
$totalHospitals = Database::fetchColumn("SELECT COUNT(*) FROM providers WHERE type = 'Hospital'");
$totalClinics = Database::fetchColumn("SELECT COUNT(*) FROM providers WHERE type = 'Clinic'");
$avgRating = Database::fetchColumn("SELECT AVG(rating) FROM providers");

// Get distinct specialties and locations for filters
$specialties = Database::fetchAll("SELECT DISTINCT specialty FROM providers WHERE specialty IS NOT NULL AND specialty != '' ORDER BY specialty");
$locations = Database::fetchAll("SELECT DISTINCT location FROM providers WHERE location IS NOT NULL ORDER BY location");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmission();
}

function handleAjaxRequest()
{
    // Set JSON header for all AJAX responses
    header('Content-Type: application/json');

    $action = $_GET['action'] ?? '';
    switch ($action) {
        case 'get_providers':
            $providers = Database::fetchAll("SELECT * FROM providers");
            echo json_encode($providers);
            break;

        /* Filtering removed: search_providers case intentionally omitted */

        case 'get_provider':
            $id = intval($_GET['id'] ?? 0);

            if ($id > 0) {
                // Use fetchAll to get array of results
                $provider = Database::fetchAll("SELECT * FROM providers WHERE id = ?", [$id]);

                if (!empty($provider)) {
                    // Return the first (and should be only) provider
                    echo json_encode($provider[0]);
                } else {
                    echo json_encode(['error' => 'Provider not found']);
                }
            } else {
                echo json_encode(['error' => 'Invalid provider ID']);
            }
            break;

        case 'get_stats':
            $totalProviders = Database::fetchColumn("SELECT COUNT(*) FROM providers");
            $totalHospitals = Database::fetchColumn("SELECT COUNT(*) FROM providers WHERE type = 'Hospital'");
            $totalClinics = Database::fetchColumn("SELECT COUNT(*) FROM providers WHERE type = 'Clinic'");
            $avgRating = Database::fetchColumn("SELECT AVG(rating) FROM providers");

            echo json_encode([
                'totalProviders' => $totalProviders,
                'totalHospitals' => $totalHospitals,
                'totalClinics' => $totalClinics,
                'avgRating' => number_format($avgRating, 1)
            ]);
            break;
    }
    exit;
}

function handleFormSubmission()
{
    if (isset($_POST['save_provider'])) {
        $id = $_POST['provider_id'] ?? 0;
        $data = [
            'name' => $_POST['name'],
            'type' => $_POST['type'],
            'location' => $_POST['location'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'specialty' => $_POST['specialty'] ?? '',
            'rating' => $_POST['rating'] ?? 0,
            'emergency' => isset($_POST['emergency']) ? 1 : 0
        ];

        if ($id > 0) {
            // Update existing provider
            Database::updateTable('providers', $data, 'id = ?', [$id]);
            $_SESSION['message'] = 'Provider updated successfully!';
            $_SESSION['message_type'] = 'success';
        } else {
            // Insert new provider
            Database::insertInto('providers', $data);
            $_SESSION['message'] = 'Provider added successfully!';
            $_SESSION['message_type'] = 'success';
        }

        header('Location: HMO_provider_network.php');
        exit;
    }

    if (isset($_POST['delete_provider'])) {
        $id = $_POST['provider_id'] ?? 0;
        if ($id > 0) {
            Database::execute('DELETE FROM providers WHERE id = ?', [$id]);
            $_SESSION['message'] = 'Provider deleted successfully!';
            $_SESSION['message_type'] = 'success';
        }
        header('Location: HMO_provider_network.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMO Provider Network</title>
    <?php include '../INCLUDES/header.php'; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .provider-card {
            transition: all 0.3s ease;
        }

        .provider-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="bg-base-100 bg-white min-h-screen">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include '../INCLUDES/sidebar.php'; ?>

        <!-- Content Area -->
        <div class="flex flex-col flex-1 overflow-auto">
            <!-- Navbar -->
            <?php include '../INCLUDES/navbar.php'; ?>

            <!-- Main Content -->
            <div class="flex flex-col flex-1 overflow-auto">
                <!-- Navbar -->
                <header class="bg-white shadow-sm border-gray-200 border-b">
                    <div class="flex justify-between items-center px-6 py-4">
                        <h1 class="font-bold text-gray-800 text-2xl">HMO Provider Network</h1>
                        <div class="flex items-center space-x-4">
                            <button onclick="openAddModal()" class="btn btn-primary">
                                <i data-lucide="plus" class="mr-2 w-4 h-4"></i>
                                Add Provider
                            </button>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-6">
                    <!-- Stats Cards -->
                    <div class="gap-6 grid grid-cols-1 md:grid-cols-4 mb-8">
                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Total Providers</p>
                                    <h3 id="totalProviders" class="mt-1 font-bold text-3xl"><?= $totalProviders ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Network wide</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="building-2" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Hospitals</p>
                                    <h3 id="totalHospitals" class="mt-1 font-bold text-3xl"><?= $totalHospitals ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">In network</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="home" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Clinics</p>
                                    <h3 id="totalClinics" class="mt-1 font-bold text-3xl"><?= $totalClinics ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Primary care</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="stethoscope" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white hover:bg-gray-50 shadow-2xl hover:shadow-2xl p-5 rounded-xl text-black hover:scale-105 transition-all duration-300 stat-card">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-[#001f54] text-sm">Avg Rating</p>
                                    <h3 id="avgRating" class="mt-1 font-bold text-3xl"><?= number_format($avgRating, 1) ?></h3>
                                    <p class="mt-1 text-gray-500 text-xs">Out of 5</p>
                                </div>
                                <div class="flex justify-center items-center bg-[#001f54] p-3 rounded-lg">
                                    <i data-lucide="star" class="w-6 h-6 text-[#F7B32B]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters removed from view -->

                    <!-- Provider Directory -->
                    <div class="bg-white shadow-sm p-6 border border-gray-100 rounded-xl">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-semibold text-gray-800">Provider Directory</h3>
                            <div class="flex gap-2">
                                <button onclick="exportProviders()" class="btn-outline btn">
                                    <i data-lucide="download" class="mr-2 w-4 h-4"></i>
                                    Export
                                </button>
                            </div>
                        </div>

                        <div id="providersContainer" class="gap-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                            <?php foreach ($providers as $provider): ?>
                                <div class="hover:shadow-md p-4 border border-gray-200 rounded-lg transition-shadow provider-card" data-id="<?= $provider->id ?>">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($provider->name) ?></h4>
                                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($provider->location) ?></p>
                                        </div>
                                        <div class="flex items-center">
                                            <i data-lucide="star" class="fill-current w-4 h-4 text-yellow-500"></i>
                                            <span class="ml-1 text-sm"><?= number_format($provider->rating, 1) ?></span>
                                        </div>
                                    </div>
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-gray-600 text-sm">
                                            <i data-lucide="phone" class="mr-2 w-4 h-4"></i>
                                            <?= htmlspecialchars($provider->phone) ?>
                                        </div>
                                        <div class="flex items-center text-gray-600 text-sm">
                                            <i data-lucide="map-pin" class="mr-2 w-4 h-4"></i>
                                            <?= htmlspecialchars($provider->address) ?>
                                        </div>
                                        <?php if ($provider->specialty): ?>
                                            <div class="flex items-center text-gray-600 text-sm">
                                                <i data-lucide="stethoscope" class="mr-2 w-4 h-4"></i>
                                                <?= htmlspecialchars($provider->specialty) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="<?= $provider->emergency ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?> px-2 py-1 rounded-full text-xs">
                                            <?= $provider->emergency ? '24/7 Emergency' : 'Regular Hours' ?>
                                        </span>
                                        <div class="flex gap-2">
                                            <button onclick="editProvider(<?= $provider->id ?>)" class="btn btn-ghost btn-xs">
                                                <i data-lucide="edit" class="w-3 h-3"></i>
                                            </button>
                                            <button onclick="confirmDeleteProvider(<?= $provider->id ?>)" class="text-error btn btn-ghost btn-xs">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <?php if (empty($providers)): ?>
                                <div class="col-span-3 py-12 text-center">
                                    <i data-lucide="search" class="mx-auto w-16 h-16 text-gray-300"></i>
                                    <h3 class="mt-4 font-semibold text-gray-700 text-lg">No providers found</h3>
                                    <p class="mt-2 text-gray-500">Try adjusting your search or filters</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- DaisyUI Modal for Add/Edit Provider -->
        <dialog id="providerModal" class="modal">
            <div class="bg-white/90 w-11/12 max-w-3xl text-black modal-box">
                <form method="dialog">
                    <button class="top-2 right-2 absolute btn btn-sm btn-circle btn-ghost">✕</button>
                </form>
                <h3 class="font-bold text-lg" id="modalTitle">Add New Provider</h3>

                <form id="providerForm" method="POST" action="HMO_provider_network.php" class="py-4">
                    <input type="hidden" name="provider_id" id="providerId" value="">
                    <input type="hidden" name="save_provider" value="1">

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2 mb-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Provider Name *</span>
                            </label>
                            <input type="text" name="name" id="providerName" required
                                class="w-full input input-bordered" placeholder="Enter provider name">
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Type *</span>
                            </label>
                            <select name="type" id="providerType" required class="w-full select-bordered select">
                                <option value="">Select Type</option>
                                <option value="Hospital">Hospital</option>
                                <option value="Clinic">Clinic</option>
                                <option value="Laboratory">Laboratory</option>
                                <option value="Specialist">Specialist</option>
                            </select>
                        </div>
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2 mb-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Location *</span>
                            </label>
                            <select name="location" id="providerLocation" required class="w-full select-bordered select">
                                <option value="">Select Location</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc->location) ?>">
                                        <?= htmlspecialchars($loc->location) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Phone Number *</span>
                            </label>
                            <input type="tel" name="phone" id="providerPhone" required
                                class="w-full input input-bordered" placeholder="Enter phone number">
                        </div>
                    </div>

                    <div class="mb-4 form-control">
                        <label class="label">
                            <span class="label-text">Address *</span>
                        </label>
                        <textarea name="address" id="providerAddress" rows="2" required
                            class="w-full textarea textarea-bordered" placeholder="Enter full address"></textarea>
                    </div>

                    <div class="gap-4 grid grid-cols-1 md:grid-cols-2 mb-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Specialty</span>
                            </label>
                            <select name="specialty" id="providerSpecialty" class="w-full select-bordered select">
                                <option value="">Select Specialty</option>
                                <?php foreach ($specialties as $spec): ?>
                                    <option value="<?= htmlspecialchars($spec->specialty) ?>">
                                        <?= htmlspecialchars($spec->specialty) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Rating: <span id="ratingValue" class="font-bold">4.0</span>/5</span>
                            </label>
                            <input type="range" name="rating" id="providerRating" min="0" max="5" step="0.1" value="4.0"
                                class="range range-sm range-primary"
                                oninput="document.getElementById('ratingValue').textContent = this.value">
                            <div class="flex justify-between px-2 w-full text-xs">
                                <span>0</span>
                                <span>1</span>
                                <span>2</span>
                                <span>3</span>
                                <span>4</span>
                                <span>5</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 form-control">
                        <label class="justify-start cursor-pointer label">
                            <input type="checkbox" name="emergency" id="providerEmergency" value="1"
                                class="mr-3 checkbox checkbox-primary">
                            <span class="label-text">24/7 Emergency Services Available</span>
                        </label>
                    </div>

                    <div class="modal-action">
                        <button type="button" onclick="closeProviderModal()" class="btn">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="mr-2 w-4 h-4"></i>
                            Save Provider
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <script>
            // Initialize Lucide icons
            lucide.createIcons();

            // Show notification if there's a message
            <?php if (isset($_SESSION['message'])): ?>
                showNotification('<?= $_SESSION['message'] ?>', '<?= $_SESSION['message_type'] ?? 'success' ?>');
            <?php
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            endif;
            ?>

            // Modal functions for daisyUI
            function openProviderModal() {
                document.getElementById('providerModal').showModal();
            }

            function closeProviderModal() {
                document.getElementById('providerModal').close();
            }

            function openAddModal() {
                document.getElementById('modalTitle').textContent = 'Add New Provider';
                document.getElementById('providerForm').reset();
                document.getElementById('providerId').value = '';
                document.getElementById('ratingValue').textContent = '4.0';
                document.getElementById('providerRating').value = 4.0;
                openProviderModal();
            }

            async function editProvider(id) {
                try {
                    console.log('Fetching provider with ID:', id);

                    const response = await fetch(`HMO_provider_network.php?action=get_provider&id=${id}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const contentType = response.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await response.text();
                        console.error('Non-JSON response:', text.substring(0, 200));
                        throw new Error('Server returned non-JSON response');
                    }

                    const data = await response.json();
                    console.log('Provider data:', data);

                    if (data.error) {
                        showNotification(data.error, 'error');
                        return;
                    }

                    if (data && data.id) {
                        document.getElementById('modalTitle').textContent = 'Edit Provider';
                        document.getElementById('providerId').value = data.id;
                        document.getElementById('providerName').value = data.name || '';
                        document.getElementById('providerType').value = data.type || '';
                        document.getElementById('providerLocation').value = data.location || '';
                        document.getElementById('providerPhone').value = data.phone || '';
                        document.getElementById('providerAddress').value = data.address || '';
                        document.getElementById('providerSpecialty').value = data.specialty || '';
                        document.getElementById('providerRating').value = data.rating || 4.0;
                        document.getElementById('ratingValue').textContent = data.rating || 4.0;
                        document.getElementById('providerEmergency').checked = data.emergency == 1;

                        openProviderModal();
                    } else {
                        showNotification('Provider not found or invalid data', 'error');
                    }
                } catch (error) {
                    console.error('Error fetching provider:', error);
                    showNotification('Error loading provider data: ' + error.message, 'error');
                }
            }

            async function searchProviders() {
                const search = document.getElementById('searchInput').value;
                const specialty = document.getElementById('specialtyFilter').value;
                const location = document.getElementById('locationFilter').value;

                try {
                    const response = await fetch(`HMO_provider_network.php?action=search_providers&search=${encodeURIComponent(search)}&specialty=${encodeURIComponent(specialty)}&location=${encodeURIComponent(location)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const providers = await response.json();

                    updateProvidersContainer(providers);

                    // Update stats
                    const statsResponse = await fetch('HMO_provider_network.php?action=get_stats', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (statsResponse.ok) {
                        const stats = await statsResponse.json();
                        updateStats(stats);
                    }

                } catch (error) {
                    console.error('Error searching providers:', error);
                    showNotification('Error searching providers: ' + error.message, 'error');
                }
            }

            function updateProvidersContainer(providers) {
                const container = document.getElementById('providersContainer');
                const showingCount = document.getElementById('showingCount');

                if (providers.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-3 py-12 text-center">
                            <i data-lucide="search" class="mx-auto w-16 h-16 text-gray-300"></i>
                            <h3 class="mt-4 font-semibold text-gray-700 text-lg">No providers found</h3>
                            <p class="mt-2 text-gray-500">Try adjusting your search or filters</p>
                        </div>
                    `;
                } else {
                    let html = '';
                    providers.forEach(provider => {
                        html += `
                            <div class="hover:shadow-md p-4 border border-gray-200 rounded-lg transition-shadow provider-card" data-id="${provider.id}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">${escapeHtml(provider.name)}</h4>
                                        <p class="text-gray-600 text-sm">${escapeHtml(provider.location)}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="star" class="fill-current w-4 h-4 text-yellow-500"></i>
                                        <span class="ml-1 text-sm">${provider.rating ? provider.rating.toFixed(1) : '0.0'}</span>
                                    </div>
                                </div>
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <i data-lucide="phone" class="mr-2 w-4 h-4"></i>
                                        ${escapeHtml(provider.phone)}
                                    </div>
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <i data-lucide="map-pin" class="mr-2 w-4 h-4"></i>
                                        ${escapeHtml(provider.address)}
                                    </div>
                                    ${provider.specialty ? `
                                    <div class="flex items-center text-gray-600 text-sm">
                                        <i data-lucide="stethoscope" class="mr-2 w-4 h-4"></i>
                                        ${escapeHtml(provider.specialty)}
                                    </div>` : ''}
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="${provider.emergency ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'} px-2 py-1 rounded-full text-xs">
                                        ${provider.emergency ? '24/7 Emergency' : 'Regular Hours'}
                                    </span>
                                    <div class="flex gap-2">
                                        <button onclick="editProvider(${provider.id})" class="btn btn-ghost btn-xs">
                                            <i data-lucide="edit" class="w-3 h-3"></i>
                                        </button>
                                        <button onclick="confirmDeleteProvider(${provider.id})" class="text-error btn btn-ghost btn-xs">
                                            <i data-lucide="trash-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    container.innerHTML = html;
                    lucide.createIcons();
                }

                if (showingCount) showingCount.textContent = providers.length;
            }

            function updateStats(stats) {
                const elTotal = document.getElementById('totalProviders');
                const elHosp = document.getElementById('totalHospitals');
                const elClin = document.getElementById('totalClinics');
                const elAvg = document.getElementById('avgRating');
                const elTotalCount = document.getElementById('totalCount');

                if (elTotal) elTotal.textContent = stats.totalProviders;
                if (elHosp) elHosp.textContent = stats.totalHospitals;
                if (elClin) elClin.textContent = stats.totalClinics;
                if (elAvg) elAvg.textContent = stats.avgRating;
                if (elTotalCount) elTotalCount.textContent = stats.totalProviders;
            }

            function confirmDeleteProvider(id) {
                // Get provider name for better confirmation message
                const providerCard = document.querySelector(`.provider-card[data-id="${id}"]`);
                const providerName = providerCard ? providerCard.querySelector('h4').textContent : 'this provider';

                Swal.fire({
                    title: 'Delete Provider?',
                    html: `Are you sure you want to delete <strong>${escapeHtml(providerName)}</strong>?<br><br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return fetch('HMO_provider_network.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `provider_id=${id}&delete_provider=1`
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(response.statusText);
                                }
                                return response.text();
                            })
                            .catch(error => {
                                Swal.showValidationMessage(
                                    `Request failed: ${error}`
                                );
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            }

            function exportProviders() {
                // Create CSV content
                let csvContent = "data:text/csv;charset=utf-8,";
                csvContent += "Name,Type,Location,Phone,Address,Specialty,Rating,Emergency\n";

                // Get all provider cards
                const providerCards = document.querySelectorAll('.provider-card');
                providerCards.forEach(card => {
                    const name = card.querySelector('h4').textContent;
                    const location = card.querySelector('.text-sm').textContent;
                    const phone = card.querySelector('[data-lucide="phone"]').parentElement.textContent.trim();
                    const address = card.querySelector('[data-lucide="map-pin"]').parentElement.textContent.trim();
                    const specialtyEl = card.querySelector('[data-lucide="stethoscope"]');
                    const specialty = specialtyEl ? specialtyEl.parentElement.textContent.trim() : '';
                    const rating = card.querySelector('.ml-1').textContent;
                    const emergency = card.querySelector('span').textContent.includes('24/7') ? 'Yes' : 'No';
                    const type = card.querySelector('[data-lucide="home"], [data-lucide="stethoscope"]') ?
                        (card.querySelector('[data-lucide="home"]') ? 'Hospital' : 'Clinic') : 'Other';

                    csvContent += `"${name}","${type}","${location}","${phone}","${address}","${specialty}","${rating}","${emergency}"\n`;
                });

                // Create download link
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "providers_export.csv");
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                showNotification('Export completed successfully!', 'success');
            }

            function showNotification(message, type = 'info') {
                // Remove existing notification
                const existingNotification = document.querySelector('.notification');
                if (existingNotification) {
                    existingNotification.remove();
                }

                const notification = document.createElement('div');
                notification.className = `notification px-6 py-3 rounded-lg shadow-lg text-white ${type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'}`;
                notification.innerHTML = `
                    <div class="flex items-center">
                        <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'info'}" class="mr-2 w-5 h-5"></i>
                        <span>${message}</span>
                    </div>
                `;

                document.body.appendChild(notification);
                lucide.createIcons();

                // Auto-remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            // Filtering inputs removed from view; no search input listeners required
        </script>
</body>

</html>