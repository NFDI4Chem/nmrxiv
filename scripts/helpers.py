"""NMRshiftDB Import Helpers.

This module includes functions used to export NMRShiftDB database as an SDF file with NMReData entries,
which are to detect the locations of the raw NMR files and download them too. 
Finally, the downloaded files are unzipped and restructured for nmrXiv submission.
""" 

import os
import html
import wget
import shutil
import codecs
import zipfile
import requests
from rdkit import Chem
from io import StringIO
from rdkit.Chem.rdchem import Mol
from rdkit.Chem import SDMolSupplier

def get_sdf_as_SDMolSupplier(url, name):
    "Download an sdf file from a URL into a named file, and return an SDMolSupplier object with the entries there."
    
    print("""\nNMRShiftDB downloading script has started. Please find downloaded items in the folder 'output'. \n""")
    
    if not os.path.exists('output'):
        os.makedirs('output')
    os.chdir('./output')
    
    response = requests.get(url)
    open(name, "wb").write(response.content)
    
    return SDMolSupplier(name)

"""NMRshiftDB Import Helpers.

This module includes functions used to export NMRShiftDB database as an SDF file with NMReData entries,
which are to detect the locations of the raw NMR files and download them too. 
Finally, the downloaded files are unzipped and restructured for nmrXiv submission.
""" 



def get_authors(mol):
    """Returns the authors from the corresponding tag in an rdkit.Chem.rdchem Mol (mol)"""
    
    # Authors have different tags, but they are always the second tag.
    authors_tag = mol.GetPropNames()[1]
    authors = Mol.GetProp(mol, authors_tag)
    
    # Remove article name
    if ':' in authors:
        authors = authors[:authors.find(':')]
        
    
    Nils_names = ["Nils Schoerer", "Nils Schloerer"]
    if authors in Nils_names: 
        authors = "Nils Schlörer"

    return authors

def get_molecule_id(mol): 
    """Returns the molecule NMRShiftDB ID from the corresponding tag NMREDATA_ID 
    in a rdkit.Chem.rdchem Mol (mol)"""

    value = Mol.GetProp(mol, 'NMREDATA_ID')
    ID = value[value.find('DB_ID=')+6: -1]
    
    return ID

def get_links(mol):
    """Returns the all the spectra links from one rdkit.Chem.rdchem Mol (mol) with the corresponding tag,
    which corresponds to one NMReData block."""
    
    spectra_tags = [tag for tag in mol.GetPropNames() if "1D" in tag or "2D" in tag]
    tag_links = {}
    
    for tag in spectra_tags:
        value = Mol.GetProp(mol, tag)
        if 'http' in value:
            link = value[value.find('=')+1:value.find('\\')]
            if ' ' in link:
                link = link.replace(' ', '%20')
            tag_links[tag] = link
    
    return tag_links



def get_name(mol):
    """Returns the molecule chemical name from the corresponding tag CHEMNAME 
    in a rdkit.Chem.rdchem Mol (mol)"""
    
    synonyms = ['caffeine', 'Piperine', 'Chamazulene', 'alpha-Onocerin', 'rosmarinic acid', 
         'Indigo', 'Raffinose', 'carminic acid', 'Quinine', 'Capsanthin', 'Betulic acid','Thymoquinone',
        'Parasorboside', 'Nitogenin', 'l-Linalool', 'Eucalyptol', 'TRANS ANETHOL', 'Dronabinol', 'Strychnine',
        'Parasorbic acid', 'patchouli alcohol', 'Limonene', '-Thujone', 'Galantamine', 'Shikimic acid',
        'Ibuprofen', 'Nicotine', 'beta-Thujone', 'Sorbic acid', 'Brazilein', 'Pulegone',
        'EUGENOL', 'Chitosamine', 'Abietic acid', 'Friedelin',  'Arbutin', 
        'beta Glucosamine Hydrochloride','beta Lactose']
    
    nils = ['beta-D-Glucopyranoside', 'beta-D-Galactopyranoside', 'alpha-D-Mannopyranoside', 
            'alpha-D-Glucopyranoside', 'alpha-D-Galactopyranoside']
    
    name = Mol.GetProp(mol, 'CHEMNAME')
    
    # removing the synonyms
    if 'InChI' in name and name[:5]=="InChI":
        name = name[:name.find(' ')]
    elif "IUPAC from" in name:
        name = name[:name.find(' (IUPAC from')]
    elif '; ' in name:
        for synonym in synonyms:
            if synonym in name:
                name = synonym 
                break
        else:
            if 'beta;-D-' in name or 'alpha;-D-' in name:
                name = name.replace(';', '')
                for synonym in nils:
                    if synonym in name:
                        name = synonym 
                        break
            else:
                name = name[:name.find('; ')]
    elif '  ' in name:
        name = name[:name.find('  ')]
    
    #ensuring the molecule name doesn't affect the files pathes
    if '/' in name:
        name = name.replace('/', '\\')
        
    # replacing ascii characters with special HTML characters
    name = html.unescape(name)
    return name

def get_solvent(mol):
    """Returns the NMR solvent from the corresponding tag NMREDATA_SOLVENT 
    in a rdkit.Chem.rdchem Mol (mol)"""
    
    solvent = Mol.GetProp(mol, 'NMREDATA_SOLVENT')
    if solvent == '(CD3\\':
        solvent = 'unknown-'
        
    #ensuring the molecule name doesn't affect the files pathes
    if '/' in solvent:
        solvent = solvent.replace('/', ', ')
   
    solvent = solvent[:-1]
    return solvent

def get_temperature(mol):
    """Returns the temperature from the corresponding tag NMREDATA_TEMPERATURE 
    in a rdkit.Chem.rdchem Mol (mol)"""
    
    try:
        temperature = Mol.GetProp(mol, 'NMREDATA_TEMPERATURE')
        temperature = temperature[:temperature.find(' ')]
    except:
        temperature = "unknown"
    
    return temperature

def get_assay_name(mol):
    """A assay conveys the molecule, solvent, and temperature. 
    Its name has the NMRShiftDB molecule ID with the names of the molecule,  solvent and the temperature, 
    taken from a rdkit.Chem.rdchem Mol (mol)"""
    
    ID =get_molecule_id(mol)
    name = get_name(mol)
    solvent = get_solvent(mol)
    temperature = get_temperature(mol)
    assay_name = ID+ '_' + name+ '_'+ solvent + '_'+ temperature
    
    return assay_name


def write_nmredata(mol, path):
    """Writes a rdkit.Chem.rdchem Mol molecule in an NMReData file in a specified path"""
    
    sio = StringIO()
    writer = Chem.SDWriter(sio)
    writer.write(mol)
    writer.close()
    sio.seek(0)    
    
    open(path, "wb").write(str.encode(sio.read()))
    
    pass

def download_zips(MolSupplier):
    print("""Downloading experimental NMR files. Here you can see the authors names from NMRShiftDB whose files are being downloaded: \n""")

    number_of_projects =0
    number_of_molecules =0
    number_of_spectra =0

    

    for mol in MolSupplier:
        authors = get_authors(mol)
        molecule_name = get_name(mol)
        assay_name = get_assay_name(mol)

       
        if not os.path.exists(authors):
            print(authors)
            number_of_projects +=1
            os.makedirs(authors)
        os.chdir('./'+ authors)

        if not os.path.exists(molecule_name):
            number_of_molecules +=1
            os.makedirs(molecule_name)
        os.chdir('./'+ molecule_name)
        
        if not os.path.exists(assay_name):
            os.makedirs(assay_name)
        os.chdir('./'+ assay_name)
        write_nmredata(mol, assay_name+'.nmredata.sdf')
        

        tag_links = get_links(mol)
        number_of_spectra += len(tag_links)
 
        for tag in tag_links:
            if not os.path.exists(tag):
                os.makedirs(tag)
            os.chdir('./'+ tag)
            wget.download(tag_links[tag])
            os.chdir("..")
        os.chdir("../../..")
        
        
    return [number_of_projects, number_of_molecules, number_of_spectra]

def unzipper():
    """Create folders for each dataset and unzip the downloaded spectrum file there. 
    Then delete the zip files."""
    
    print("""Unzipping spectra files in the corresponding created datasets folders. This might take a little while. Following, you can find the names of the authors folders where the spectra files are getting unzipped.\n""")    

    for authors in os.listdir("./"):
        if os.path.isdir("./" + authors):
            print(authors)
            project_folder = os.getcwd() + '/' + authors
            for molecule in os.listdir(project_folder):
                if os.path.isdir(project_folder + '/' + molecule):
                    study_folder = project_folder + '/' + molecule
                    for assay in os.listdir(study_folder):
                        if os.path.isdir(study_folder + '/' + assay):
                            assay_folder = study_folder + '/' + assay
                            for spectrum in os.listdir(assay_folder):
                                if os.path.isdir(assay_folder + '/' + spectrum):
                                    spectrum_folder = assay_folder + '/' + spectrum
                                    for file in os.listdir(spectrum_folder):
                                        if '.zip' in spectrum_folder + '/' +file:
                                            try:
                                                with zipfile.ZipFile(spectrum_folder + '/' +file, 'r') as zip_ref:
                                                    zip_ref.extractall(spectrum_folder)
                                                os.remove(spectrum_folder + '/' +file)
                                            except:
                                                print(spectrum_folder + '/' +file)
                                                        
    for path, directories, files in os.walk("./"):
        for file in files:
            if '.zip' in file:
                with zipfile.ZipFile(os.path.join(path, file), 'r') as zip_ref:
                    zip_ref.extractall(path)
                os.remove(os.path.join(path, file))
              
               
    print('\nunzipping has ended')
    pass

def get_Bruker_number(innerFolder):
    """Find the Bruker instrument original sample number from 'acqu' file."""
    
    if "acqu" in os.listdir(innerFolder):
        with codecs.open(innerFolder + '/acqu', 'r', encoding='utf-8',
                         errors='ignore') as f:

            lines = f.readlines()
        for line in lines:
            if 'acqu' in line:
                break
        else:
            print(innerFolder)
        line = line[:line.rfind('/acqu')]
        if line[-1] == '/':
            line = line[:-1]
        line = line[line.rfind('/')+1:]
        
    else:
        line = -1
    
    return line
    
    


def rename_folders():

    """Rename the Bruker folders for spectra that were initially named by users to 
    their original instrument name."""
    n = 0
    for authors in os.listdir("./"):
        if os.path.isdir("./" + authors):
            project_folder = os.getcwd() + '/' + authors
            print(authors)
            for molecule in os.listdir(project_folder):
                if os.path.isdir(project_folder + '/' + molecule):
                    study_folder = project_folder + '/' + molecule
                    for assay in os.listdir(study_folder):
                        if os.path.isdir(study_folder + '/' + assay):
                            assay_folder = study_folder + '/' + assay
                            if os.path.isdir(assay_folder):
                                tags_dict = {}
                                for item in os.listdir(assay_folder):
                                    item_path = assay_folder + '/' + item
                                    if 'nmredata' in item:
                                        nmredata = item_path
                                    elif os.path.isdir(item_path):
                                        innerFolder = item_path
                                        if 'META-INF' in os.listdir(innerFolder):
                                            path = innerFolder + '/META-INF/' 
                                            shutil.rmtree(path)
                                            
                                        while ("acqu" not in os.listdir(innerFolder)) and ("fid" not in os.listdir(innerFolder) and "ser" not in os.listdir(innerFolder)):
                                            if 'pdata' in os.listdir(innerFolder):
                                                parent = innerFolder[:innerFolder.rfind('/')]
                                                
                                                shutil.rmtree(innerFolder)
                                                innerFolder = parent
                                                
                                
                                            for item2 in os.listdir(innerFolder):                
                                                    if os.path.isdir(innerFolder + '/' + item2):
                                                        innerFolder +=  '/' + item2
                                        
                                        if "acqu" not in os.listdir(innerFolder):
                                            if "PROTON" in innerFolder:
                                                n = "1"
                                            elif "CARBON" in innerFolder:
                                                n = "2"
                                        else:
                                            n = get_Bruker_number(innerFolder)
                                        print(n)
                                        suffix = innerFolder[innerFolder.rfind("/")+1:]
                                        target = innerFolder[:innerFolder.rfind(suffix)] +n
                                        os.rename(innerFolder, target)
                                        tags_dict[item] = n
                                        
                                mol = SDMolSupplier(nmredata)[0]
                                for tag in tags_dict:
                                    mol.SetProp(tag, 'Spectrum_Location=./' + tags_dict[tag])

                                write_nmredata(mol, nmredata)
    
                                        
    print("""Spectra folders were renamed to their original instrument number.""")
    
def structure_folders():
    """Move files and folders to restructure them in a way suitable for nmrXiv submission."""
    print('\npreparing the folders structure for proper submision to nmrXiv.\n')
    for authors in os.listdir("./"):
        if os.path.isdir("./" + authors):
            project_folder = os.getcwd() + '/' + authors
            for molecule in os.listdir(project_folder):
                if os.path.isdir(project_folder + '/' + molecule):
                    study_folder = project_folder + '/' + molecule
                    for assay in os.listdir(study_folder):
                        
                        if os.path.isdir(study_folder + '/' + assay):
                            assay_folder = study_folder + '/' + assay
                            
                            if os.path.isdir(assay_folder):
                                for item in os.listdir(assay_folder):
                                    item_path = assay_folder + '/' + item
                                    
                                    if 'nmredata' in item:
                                        shutil.move(item_path, study_folder) 
                                        
                                    elif os.path.isdir(item_path):
                                        innerFolder = item_path
                                        while ("acqu" not in os.listdir(innerFolder)) and ("fid" not in os.listdir(innerFolder) and "ser" not in os.listdir(innerFolder)):
                                            for item in os.listdir(innerFolder):
                                                if os.path.isdir(innerFolder + '/' + item):
                                                    innerFolder +=  '/' + item


                                        if innerFolder[innerFolder.rfind('/')+1:] not in os.listdir(study_folder):
                                            try:
                                                shutil.move(innerFolder, study_folder) 
                                            except:
                                                print(innerFolder)
                                        else:
                                            new = innerFolder+'#2'
                                            os.rename(innerFolder, new)
                                            if new[new.rfind('/')+1:] not in os.listdir(study_folder):
                                                try:
                                                    shutil.move(new, study_folder)
                                                except:
                                                    print('new: ' +  new)
                                            else:
                                                new2 = new+'#3'
                                                os.rename(new, new2)
                                                if new2[new2.rfind('/')+1:] not in os.listdir(study_folder):
                                                    try:
                                                        shutil.move(new2, study_folder)
                                                    except:
                                                        print('new2: ' + new2)
                                               
                                                    
                                            
    pass
  

def delete_empty_folders(root):

    deleted = set()
    
    for current_dir, subdirs, files in os.walk(root, topdown=False):

        still_has_subdirs = any(
            subdir for subdir in subdirs
            if os.path.join(current_dir, subdir) not in deleted
        )
    
        if not any(files) and not still_has_subdirs:
            os.rmdir(current_dir)
            deleted.add(current_dir)

    return deleted

def counter():
    
    d3 = 0
    d2 = 0
    d1 = 0
    for authors in os.listdir("./"):
        if os.path.isdir("./" + authors):
            project_folder = os.getcwd() + '/' + authors
            for molecule in os.listdir(project_folder):
                if os.path.isdir(project_folder + '/' + molecule):
                    study_folder = project_folder + '/' + molecule
                    d_dict = {'d3':0, 'd2':0,'d1':0}
                    for item in os.listdir(study_folder):
                        if 'nmredata' in item:
                            nmredata = study_folder + '/' + item
                            mol = SDMolSupplier(nmredata)[0]
                            spectra_tags = [tag for tag in mol.GetPropNames() if "1D" in tag or "2D" in tag]

                            if any('1D' in tag for tag in spectra_tags) and any('2D' in tag for tag in spectra_tags):
                                d_dict['d3'] += 1
                            elif any('2D' in tag for tag in spectra_tags):
                                 d_dict['d2'] += 1
                            elif any('1D' in tag for tag in spectra_tags):
                                d_dict['d1'] += 1
                    if (d_dict['d3'] > 0) or (d_dict['d2'] > 0 and d_dict['d1'] > 0):
                        d3 += 1
                    elif d_dict['d2'] > 0 and d_dict['d3'] == 0: 
                        d2 += 1
                    elif d_dict['d1'] > 0 and d_dict['d3'] == 0:
                        d1 += 1
                    else:
                        print(study_folder)
    return [d3,d2,d1]
                  