import { Component, OnInit, OnChanges, Input } from '@angular/core';
import { RepositoryService } from '../_service/repository.service';
import { PublishTokenService } from '../_service/publishtoken.service';


@Component({
  selector: 'app-publish-tokens',
  templateUrl: './publish-tokens.component.html',
  styleUrls: ['./publish-tokens.component.css']
})
export class PublishTokensComponent implements OnInit {

        displayDialog: boolean;
      
          @Input()
          item: any;
            
            @Input()
            selectedItem: any;
            
            newItem: boolean;
        
            items: any[];

            repos:any[];

            map: string= "QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm1234567890";
      
        constructor(private itemService: PublishTokenService, private repoService:RepositoryService) { }
      
        ngOnInit() {
          this.reload();
          this.loadRepos();
       }

 

    loadRepos()
    {
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
      }      );
    }
    nameChange(changes) {
      console.log(changes);
   
      if (this.item.isNew)
      {
        console.log(this.repoService.slugify(changes));
        this.item.reposlug = this.repoService.slugify(changes)
      }
  
      if ( this.item.reposlug.length < 10)
      {
        this.item.reposlug = (this.item.reposlug + "000000000") ;
      }
      this.item.reposlug = this.item.reposlug.substring(0, 10)
    }
       reload()
       {
          this.itemService.getList().then(items => this.items = items);
       }
      
      showDialogToAdd() {
          this.newItem = true;
          this.item = {isNew: true};
          this.generateReader();
          this.generateWriter();
          this.displayDialog = true;
          this.item.reposlug = this.repos[0].value;
      }
      
      closeDialog() {
        this.displayDialog = false;
      }
      save() {
          let items = [...this.items];
          if (this.newItem)
              items.push(this.item);
          else
              items[this.findSelectedCarIndex()] = this.item;
      
          this.itemService.save(this.item).then(item => this.reload());
      
          this.items = items;
          this.item = null;
          this.displayDialog = false;
      }
      
      delete() {
         this.itemService.delete(this.item).then(item => this.reload());
          let index = this.findSelectedCarIndex();
          this.items = this.items.filter((val, i) => i != index);
          this.item = null;    
          this.displayDialog = false;
      }    
      
      onRowSelect(event) {
          this.newItem = false;
          this.item = this.cloneCar(event.data);
          this.displayDialog = true;
      }
      
      cloneCar(c: any): any {
          let item = {};
          for (let prop in c) {
              item[prop] = c[prop];
          }
          return item;
      }

      findSelectedCarIndex(): number {
          return this.items.indexOf(this.selectedItem);
      }

     

      generateCode(len)
      {
        let code ='';
        for (let i = 0; i < 30; i++)
        {
          //console.log(Math.random());

           code += this.map[Math.floor(Math.random() * (this.map.length))] ;
        }
        return code;
      } 
      generateWriter()
      {
        
          this.item.writetoken =  this.generateCode(30);
      }
      generateReader()
      {
        this.item.readtoken =   this.generateCode(30);
      }
      
}
      
      
      
