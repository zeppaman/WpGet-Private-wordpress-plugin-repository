import { Component, OnInit } from '@angular/core';
import { PackageService } from '../_service/package.service';
import { RepositoryService } from '../_service/repository.service';

@Component({
  selector: 'app-packages',
  templateUrl: './packages.component.html',
  styleUrls: ['./packages.component.css']
})
export class PackagesComponent implements OnInit {

  constructor(private itemService:PackageService, private repoService:RepositoryService) { }


  items: any[];
  repos:any[];
  search:any={reposlug:"",name:""};

  ngOnInit() {
    this.reload();
    this.loadRepos();
    this.search.reposlug="";
 }

 doSearch()
 {
  this.reload();
  
 }

 loadRepos()
 {
   this.repoService.getList().then(items => 
    { 
      let result: any[] =[] ;
      result.push({'label':"ALL", reposlug:""});
     items.forEach(element => {
       let resultItem :any={};
       resultItem.label=element.name;
       resultItem.value=element.reposlug;
       //console.log(resultItem);
       result.push(resultItem);          
     }); 
     this.repos=result;
   }      );
 }

 reload()
 {
    this.itemService
      .getList(this.search.reposlug, this.search.name)
      .then(items => this.items = items);
 }
}
