import { Component, OnInit } from '@angular/core';
import { RepositoryService } from '../_service/repository.service';
import {FileUploadModule} from 'primeng/primeng';
import { AuthenticationService } from '../_service/authentication.services';
import { ConfigurationService } from '../_service/configuration.service';

@Component({
  selector: 'app-upload-package',
  templateUrl: './upload-package.component.html',
  styleUrls: ['./upload-package.component.css']
})
export class UploadPackageComponent implements OnInit {

  repos: any[];

  constructor( private repoService : RepositoryService, private authenticationService: AuthenticationService, private config:ConfigurationService) { }

  uploadSettings:any={};

  ngOnInit() {
    this.loadRepos();
    
    this.uploadSettings.url = this.config.apiHost+'catalog/Package';
    this.uploadSettings.token=this.authenticationService.currentUser().token;
  }



loadRepos(){
 this.repoService.getList().then(items =>
  {
    let result: any[] =[] ;
   items.forEach(element => {
     let resultItem :any={};
     resultItem.label=element.name;
     resultItem.value=element.reposlug;
     console.log(resultItem);
     result.push(resultItem);
   }); 
   this.repos=result;
 });
}

}
