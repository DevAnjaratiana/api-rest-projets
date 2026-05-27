<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .btn-action { margin: 0 3px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-users"></i>
                    Gestion des Clients
                </h1>
                <p class="text-muted">Application qui communiquera avec l'API Commandes</p>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0" id="formTitle">Nouveau Client</h5>
            </div>
            <div class="card-body">
                <form id="clientForm">
                    <input type="hidden" id="client_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom *</label>
                            <input type="text" class="form-control" id="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label">Téléphone *</label>
                            <input type="text" class="form-control" id="telephone" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pays" class="form-label">Pays</label>
                            <input type="text" class="form-control" id="pays" value="France">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="adresse">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="ville">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="code_postal" class="form-label">Code postal</label>
                            <input type="text" class="form-control" id="code_postal">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="2"></textarea>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" id="btnSave">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnCancel" style="display: none;">
                            <i class="fas fa-times"></i> Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des clients -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Clients</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Ville</th>
                                <th>Pays</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="clientsList">
                            <tr>
                                <td colspan="7" class="text-center">Chargement...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce client ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_URL = 'http://localhost:8001/api/clients';
        let currentDeleteId = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            loadClients();
            document.getElementById('clientForm').addEventListener('submit', saveClient);
            document.getElementById('btnCancel').addEventListener('click', resetForm);
            document.getElementById('confirmDelete').addEventListener('click', deleteClient);
        });

        async function loadClients() {
            try {
                const response = await fetch(API_URL);
                const clients = await response.json();
                displayClients(clients);
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement');
            }
        }

        function displayClients(clients) {
            const tbody = document.getElementById('clientsList');
            if (clients.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">Aucun client</td></tr>';
                return;
            }

            tbody.innerHTML = clients.map(client => `
                <tr>
                    <td>${client.id}</td>
                    <td><strong>${escapeHtml(client.nom)}</strong></td>
                    <td>${escapeHtml(client.email)}</td>
                    <td>${escapeHtml(client.telephone)}</td>
                    <td>${escapeHtml(client.ville || '-')}</td>
                    <td>${escapeHtml(client.pays || 'France')}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-action" onclick="editClient(${client.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-action" onclick="showDeleteModal(${client.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                     </td>
                 `
            ).join('');
        }

        async function saveClient(event) {
            event.preventDefault();
            const id = document.getElementById('client_id').value;
            const clientData = {
                nom: document.getElementById('nom').value,
                email: document.getElementById('email').value,
                telephone: document.getElementById('telephone').value,
                adresse: document.getElementById('adresse').value,
                ville: document.getElementById('ville').value,
                code_postal: document.getElementById('code_postal').value,
                pays: document.getElementById('pays').value,
                notes: document.getElementById('notes').value
            };

            try {
                let response;
                if (id) {
                    response = await fetch(`${API_URL}/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(clientData)
                    });
                } else {
                    response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(clientData)
                    });
                }

                if (response.ok) {
                    resetForm();
                    loadClients();
                    alert(id ? 'Client modifié!' : 'Client créé!');
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement');
            }
        }

        async function editClient(id) {
            try {
                const response = await fetch(`${API_URL}/${id}`);
                const client = await response.json();

                document.getElementById('client_id').value = client.id;
                document.getElementById('nom').value = client.nom;
                document.getElementById('email').value = client.email;
                document.getElementById('telephone').value = client.telephone;
                document.getElementById('adresse').value = client.adresse || '';
                document.getElementById('ville').value = client.ville || '';
                document.getElementById('code_postal').value = client.code_postal || '';
                document.getElementById('pays').value = client.pays || 'France';
                document.getElementById('notes').value = client.notes || '';

                document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier Client';
                document.getElementById('btnCancel').style.display = 'inline-block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (error) {
                alert('Erreur lors du chargement');
            }
        }

        function resetForm() {
            document.getElementById('clientForm').reset();
            document.getElementById('client_id').value = '';
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus"></i> Nouveau Client';
            document.getElementById('btnCancel').style.display = 'none';
            document.getElementById('pays').value = 'France';
        }

        function showDeleteModal(id) {
            currentDeleteId = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        async function deleteClient() {
            try {
                const response = await fetch(`${API_URL}/${currentDeleteId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });

                if (response.ok) {
                    loadClients();
                    alert('Client supprimé!');
                }
            } catch (error) {
                alert('Erreur lors de la suppression');
            }
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
    </script>
</body>
</html>
