<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gestion des Commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); border: none; margin-bottom: 20px; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.85em; font-weight: 500; }
        .status-en_attente { background-color: #ffc107; color: #000; }
        .status-confirmee { background-color: #17a2b8; color: #fff; }
        .status-expediee { background-color: #6f42c1; color: #fff; }
        .status-livree { background-color: #28a745; color: #fff; }
        .status-annulee { background-color: #dc3545; color: #fff; }
        .btn-action { margin: 0 3px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-shopping-cart"></i>
                    Gestion des Commandes
                </h1>
                <p class="text-muted">Application qui communiquera avec l'API Clients</p>
            </div>
        </div>

        <!-- Formulaire -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0" id="formTitle">Nouvelle Commande</h5>
            </div>
            <div class="card-body">
                <form id="commandeForm">
                    <input type="hidden" id="commande_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_nom" class="form-label">Nom du client *</label>
                            <input type="text" class="form-control" id="client_nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="client_email" class="form-label">Email du client *</label>
                            <input type="email" class="form-control" id="client_email" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="produit" class="form-label">Produit *</label>
                            <input type="text" class="form-control" id="produit" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="montant" class="form-label">Montant (€) *</label>
                            <input type="number" step="0.01" class="form-control" id="montant" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-control" id="statut">
                                <option value="en_attente">En attente</option>
                                <option value="confirmee">Confirmée</option>
                                <option value="expediee">Expediée</option>
                                <option value="livree">Livrée</option>
                                <option value="annulee">Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_commande" class="form-label">Date commande *</label>
                            <input type="date" class="form-control" id="date_commande" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_livraison_prevue" class="form-label">Date livraison prévue</label>
                            <input type="date" class="form-control" id="date_livraison_prevue">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="client_id" class="form-label">ID Client (depuis l'app Clients) *</label>
                            <input type="number" class="form-control" id="client_id" required>
                            <small class="text-muted">Entrez l'ID du client existant dans l'application Clients</small>
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

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Liste des Commandes</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Email</th>
                                <th>Produit</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date commande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="commandesList">
                            <tr>
                                <td colspan="8" class="text-center">Chargement...</td>
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
                    Êtes-vous sûr de vouloir supprimer cette commande ?
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
        const API_URL = 'http://localhost:8000/api/commandes';
        let currentDeleteId = null;

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            loadCommandes();
            document.getElementById('commandeForm').addEventListener('submit', saveCommande);
            document.getElementById('btnCancel').addEventListener('click', resetForm);
            document.getElementById('confirmDelete').addEventListener('click', deleteCommande);
            document.getElementById('date_commande').valueAsDate = new Date();
            loadClientsForSelect();
        });

        async function loadCommandes() {
            try {
                const response = await fetch(API_URL);
                const commandes = await response.json();
                displayCommandes(commandes);
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors du chargement');
            }
        }

        function displayCommandes(commandes) {
        const tbody = document.getElementById('commandesList');
        if (commandes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="text-center">Aucune commande</td></tr>';
            return;
        }

        tbody.innerHTML = commandes.map(commande => `
            <tr>
                <td>${commande.id}</td>
                <td><strong>Client ID: ${commande.client_id}</strong><br>${escapeHtml(commande.client_nom)}</td>
                <td>${escapeHtml(commande.client_email)}</td>
                <td>${escapeHtml(commande.produit)}</td>
                <td class="text-end">${parseFloat(commande.montant).toFixed(2)} €</td>
                <td><span class="status-badge status-${commande.statut}">${commande.statut.replace('_', ' ')}</span></td>
                <td>${commande.date_commande}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-action" onclick="editCommande(${commande.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-action" onclick="showDeleteModal(${commande.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `
        ).join('');
    }

            async function saveCommande(event) {
            event.preventDefault();
            const id = document.getElementById('commande_id').value;
            const clientId = document.getElementById('client_id').value;

            const commandeData = {
                client_id: parseInt(clientId),
                client_nom: document.getElementById('client_nom').value,
                client_email: document.getElementById('client_email').value,
                produit: document.getElementById('produit').value,
                montant: parseFloat(document.getElementById('montant').value),
                statut: document.getElementById('statut').value,
                date_commande: document.getElementById('date_commande').value,
                date_livraison_prevue: document.getElementById('date_livraison_prevue').value || null,
                notes: document.getElementById('notes').value
            };

            try {
                let response;
                if (id) {
                    response = await fetch(`${API_URL}/${id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(commandeData)
                    });
                } else {
                    response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify(commandeData)
                    });
                }

                const result = await response.json();

                if (response.ok) {
                    resetForm();
                    loadCommandes();
                    alert(id ? 'Commande modifiée!' : 'Commande créée!');
                } else {
                    alert('Erreur: ' + (result.error || 'Problème lors de l\'enregistrement'));
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'enregistrement');
            }
        }

        async function editCommande(id) {
            try {
                const response = await fetch(`${API_URL}/${id}`);
                const commande = await response.json();

                document.getElementById('commande_id').value = commande.id;
                document.getElementById('client_nom').value = commande.client_nom;
                document.getElementById('client_email').value = commande.client_email;
                document.getElementById('produit').value = commande.produit;
                document.getElementById('montant').value = commande.montant;
                document.getElementById('statut').value = commande.statut;
                document.getElementById('date_commande').value = commande.date_commande;
                document.getElementById('date_livraison_prevue').value = commande.date_livraison_prevue;
                document.getElementById('notes').value = commande.notes;

                document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit"></i> Modifier Commande';
                document.getElementById('btnCancel').style.display = 'inline-block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (error) {
                alert('Erreur lors du chargement');
            }
        }

        async function loadClientsForSelect() {
        try {
            const response = await fetch('http://localhost:8001/api/clients');
            const clients = await response.json();
            const clientSelect = document.getElementById('client_id');

            // Ajouter une datalist pour suggérer les clients
            let datalist = document.getElementById('clients-list');
            if (!datalist) {
                datalist = document.createElement('datalist');
                datalist.id = 'clients-list';
                clientSelect.setAttribute('list', 'clients-list');
                document.body.appendChild(datalist);
            }

            datalist.innerHTML = clients.map(client =>
                `<option value="${client.id}">${client.id} - ${client.nom} (${client.email})</option>`
            ).join('');
        } catch (error) {
            console.error('Erreur chargement clients:', error);
        }
    }

        async function loadClientInfo() {
        const clientId = document.getElementById('client_id').value;
        if (!clientId) return;

        try {
            const response = await fetch(`http://localhost:8001/api/clients/${clientId}`);
            if (response.ok) {
                const client = await response.json();
                // Remplir automatiquement le nom et l'email
                document.getElementById('client_nom').value = client.nom;
                document.getElementById('client_email').value = client.email;
                document.getElementById('client_nom').style.backgroundColor = '#e8f0fe';
                document.getElementById('client_email').style.backgroundColor = '#e8f0fe';
            } else {
                // Client non trouvé
                document.getElementById('client_nom').value = '';
                document.getElementById('client_email').value = '';
                document.getElementById('client_nom').style.backgroundColor = '#ffe6e6';
                document.getElementById('client_email').style.backgroundColor = '#ffe6e6';
            }
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    // Ajouter l'événement sur le champ client_id
    document.getElementById('client_id').addEventListener('blur', loadClientInfo);
    document.getElementById('client_id').addEventListener('input', function() {
        // Réinitialiser les couleurs quand l'utilisateur tape
        document.getElementById('client_nom').style.backgroundColor = '';
        document.getElementById('client_email').style.backgroundColor = '';
    });

        function resetForm() {
            document.getElementById('commandeForm').reset();
            document.getElementById('commande_id').value = '';
            document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus"></i> Nouvelle Commande';
            document.getElementById('btnCancel').style.display = 'none';
            document.getElementById('date_commande').valueAsDate = new Date();
        }

        function showDeleteModal(id) {
            currentDeleteId = id;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        async function deleteCommande() {
            try {
                const response = await fetch(`${API_URL}/${currentDeleteId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });

                if (response.ok) {
                    loadCommandes();
                    alert('Commande supprimée!');
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
